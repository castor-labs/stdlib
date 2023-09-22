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

namespace Castor\Net\Http\Pipeline\HandlerFactory;

use Castor\Net\Http\Handler;
use Castor\Net\Http\Pipeline\HandlerFactory;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class ContainerBased implements HandlerFactory
{
    /**
     * @param array<string,string> $translationMap
     */
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly array $translationMap = []
    ) {
    }

    public function createMiddleware(string $name, Handler $next): Handler
    {
        $middleware = $this->getService($name);

        if (!\is_callable($middleware)) {
            $type = \gettype($middleware);

            throw new CreationError("Middleware '{$name}' fetched from container must be a callable, {$type} given");
        }

        $handler = $middleware($next);
        if (!$handler instanceof Handler) {
            $type = \gettype($handler);
            $instance = Handler::class;

            throw new CreationError("Middleware function named '{$name}' must return an instance of {$instance}, {$type} returned");
        }

        return $handler;
    }

    public function createHandler(string $name): Handler
    {
        $handler = $this->getService($name);
        if (!$handler instanceof Handler) {
            $type = \gettype($handler);
            $instance = Handler::class;

            throw new CreationError("Handler named '{$name}' must return an instance of {$instance}, {$type} returned");
        }

        return $handler;
    }

    private function getService(string $name): mixed
    {
        $service = $this->translationMap[$name] ?? $name;

        try {
            return $this->container->get($service);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            throw new CreationError("Could not create handler '{$name}' from service container", previous: $e);
        }
    }
}
