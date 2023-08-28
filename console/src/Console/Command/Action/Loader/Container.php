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

namespace Castor\Console\Command\Action\Loader;

use Castor\Console\Command\Action;
use Castor\Console\Command\Action\ExecutionError;
use Castor\Console\Command\Action\Loader;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface as PsrContainer;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Loads Actions from a PSR-11 Container.
 *
 * This loader is really well suited for applications, since you can define your loaders as
 * services and plug them lazily into your console interface.
 *
 * It allows you to map action names to services, just in case you want to define names for your commands
 * that differ from the services stored in the container, or you don't have control over the names
 * stored in the container.
 *
 * The container MUST return either a callable (which will be wrapped in a Action\Func) or an
 * instance of Action itself.
 */
final class Container implements Loader
{
    /**
     * @param array<string,string> $nameToServiceMap
     */
    public function __construct(
        private readonly PsrContainer $container,
        private array $nameToServiceMap = [],
    ) {
    }

    public function mapNameToService(string $name, string $service): void
    {
        $this->nameToServiceMap[$name] = $service;
    }

    public function load(string $name): Action
    {
        $service = $this->getServiceName($name);

        try {
            $action = $this->container->get($service);
        } catch (NotFoundExceptionInterface $e) {
            throw new NotFoundError("Action named '{$name}' could not be found in container", previous: $e);
        } catch (ContainerExceptionInterface $e) {
            throw new UnexpectedError("Action named '{$name}' could not be fetched from container", previous: $e);
        }

        if (\is_callable($action)) {
            $action = new Action\Func($action);
        }

        if ($action instanceof Action) {
            return $action;
        }

        $type = \gettype($action);
        $instance = Action::class;

        throw new ExecutionError("Action named '{$name}' returned from container must be a callable or an instance of {$instance}, {$type} given");
    }

    private function getServiceName(string $name): string
    {
        return $this->nameToServiceMap[$name] ?? $name;
    }
}
