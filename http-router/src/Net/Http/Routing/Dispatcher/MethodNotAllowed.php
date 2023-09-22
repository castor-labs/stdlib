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

namespace Castor\Net\Http\Routing\Dispatcher;

use Castor\Net\Http\HandlerError;
use Castor\Net\Http\Status;

class MethodNotAllowed extends HandlerError
{
    public readonly array $allowedMethods;

    public static function create(array $allowedMethods = [], \Throwable $previous = null): MethodNotAllowed
    {
        $self = self::fromStatus(Status::METHOD_NOT_ALLOWED, 'Method not allowed', $previous);
        $self->allowedMethods = $allowedMethods;

        return $self;
    }
}
