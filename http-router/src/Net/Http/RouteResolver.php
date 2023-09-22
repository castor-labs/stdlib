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

namespace Castor\Net\Http;

use Castor\Context;
use Castor\Net\Http\Routing\Dispatcher;

final class RouteResolver implements Handler
{
    public function __construct(
        private readonly Handler $next,
        private readonly Dispatcher $dispatcher
    ) {
    }

    /**
     * @return callable(Handler):self
     */
    public static function middleware(Dispatcher $dispatcher): callable
    {
        return static fn (Handler $next) => new self($next, $dispatcher);
    }

    /**
     * @throws HandlerError
     */
    public function handle(Context $ctx, Request $request, ResponseWriter $writer): void
    {
        try {
            $result = $this->dispatcher->dispatch($request);
        } catch (Dispatcher\MethodNotAllowed|Dispatcher\NotFound $e) {
            $result = $e;
        }

        $ctx = Routing\withResult($ctx, $result);

        $this->next->handle($ctx, $request, $writer);
    }
}
