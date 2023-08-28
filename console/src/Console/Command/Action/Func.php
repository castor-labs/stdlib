<?php

declare(strict_types=1);

/**
 * @project The Castor Standard Library
 * @link https://github.com/castor-labs/stdlib
 * @package castor/stdlib
 * @author Matias Navarro-Carter mnavarrocarter@gmail.com
 * @license MIT
 * @copyright 2022 CastorLabs Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Castor\Console\Command\Action;

use Castor\Console\Command\Action;
use Castor\Console\Command\Arg;
use Castor\Console\Command\Flag;
use Castor\Console\Session;
use Castor\Context;

final class Func implements Action
{
    public function __construct(
        private readonly \Closure $closure
    ) {
    }

    public function execute(Context $ctx, Session $cli): int
    {
        $arguments = $this->buildArguments($ctx, $cli);

        try {
            $res = ($this->closure)(...$arguments);
        } catch (\Throwable $e) {
            throw new ExecutionError('Error while executing action function', previous: $e);
        }

        if (\is_int($res)) {
            return $res;
        }

        return self::SUCCESS;
    }

    /**
     * @throws ParseError
     */
    private function buildArguments(Context $ctx, Session $cli): array
    {
        $arguments = [];

        try {
            $function = new \ReflectionFunction($this->closure);
        } catch (\ReflectionException $e) {
            throw new ExecutionError('Could not reflect command action function', previous: $e);
        }

        $parameters = $function->getParameters();
        foreach ($parameters as $parameter) {
            $type = $this->getNamedType($parameter);

            if (Context::class === $type->getName()) {
                $arguments[] = $ctx;

                continue;
            }

            if (Session::class === $type->getName()) {
                $arguments[] = $cli;

                continue;
            }

            if (!$type->isBuiltin()) {
                throw new \LogicException('Only built in types are supported for arguments');
            }

            $arguments[] = match (true) {
                $cli->hasArg($parameter->name) => $this->processArg($cli->getArg($parameter->name), $type),
                $cli->hasFlag($parameter->name) => $this->processFlag($cli->getFlag($parameter->name), $type),
                default => throw new \LogicException("Could not process parameter '{$parameter->name}' of type '{$type}'"),
            };
        }

        return $arguments;
    }

    private function getNamedType(\ReflectionParameter $param): \ReflectionNamedType
    {
        $type = $param->getType();
        if (null === $type) {
            throw new \LogicException("Parameter {$param->name} must be typed");
        }

        if ($type instanceof \ReflectionNamedType) {
            return $type;
        }

        throw new \LogicException("Union / intersection types on {$param->name} are not supported");
    }

    /**
     * @throws ParseError
     */
    private function processArg(Arg $arg, \ReflectionNamedType $type): mixed
    {
        $typeName = $type->getName();

        return match (true) {
            $arg instanceof Arg\Str && 'string' === $typeName => $arg->getValue(),
        };
    }

    private function processFlag(Flag $flag, \ReflectionNamedType $type): mixed
    {
        $typeName = $type->getName();

        return match (true) {
            $flag instanceof Flag\Boolean && 'bool' === $typeName => $flag->getValue(),
        };
    }
}
