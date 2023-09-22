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

namespace Castor\Net\Http\Routing;

use Castor\Net\Http\Request;
use Castor\Net\Http\Routing\Dispatcher\Found;
use Castor\Net\Http\Routing\Dispatcher\MethodNotAllowed;
use Castor\Net\Http\Routing\Dispatcher\NotFound;

interface Dispatcher
{
    /**
     * @throws MethodNotAllowed
     * @throws NotFound
     */
    public function dispatch(Request $request): Found;
}
