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

/**
 * Action loaders load actions for commands.
 *
 * Loaders are used for lazy actions.
 *
 * @see Action\LazyLoaded
 *
 * There are only two implementations this library provides:
 * @see Action\Loader\Container
 * @see Action\Loader\FactoryMap
 */
interface Loader
{
    /**
     * Loads an action by name.
     *
     * @throws Loader\UnexpectedError if there is an error loading the action
     * @throws Loader\NotFoundError   if the action cannot be found
     */
    public function load(string $name): Action;
}
