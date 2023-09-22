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

namespace Castor\Net\Http\Pipeline;

use Castor\Net\Http\Handler;
use Castor\Net\Http\Pipeline\HandlerFactory\CreationError;

interface HandlerFactory
{
    /**
     * @throws CreationError
     */
    public function createMiddleware(string $name, Handler $next): Handler;

    /**
     * @throws CreationError
     */
    public function createHandler(string $name): Handler;
}
