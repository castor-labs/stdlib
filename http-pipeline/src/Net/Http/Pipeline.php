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
use Castor\Net\Http\Pipeline\HandlerFactory;
use Castor\Net\Http\Pipeline\HandlerFactory\CreationError;

/**
 * A Pipeline is a lazy-loaded, recursive, middleware chain.
 */
final class Pipeline implements \Castor\Net\Http\Handler
{
    public function __construct(
        private readonly HandlerFactory $factory,
        private array $queue = []
    ) {
    }

    public function stack(string $handler): void
    {
        \array_unshift($this->queue, $handler);
    }

    public function handle(Context $ctx, Request $request, ResponseWriter $writer): void
    {
        $handler = $this->getNextHandler();
        $handler->handle($ctx, $request, $writer);
    }

    /**
     * @throws CreationError
     */
    private function getNextHandler(): Handler
    {
        if ([] === $this->queue) {
            throw new CreationError('There are no handlers in the pipeline');
        }

        // Only one remaining element in the queue. We make it without a next.
        if (1 === \count($this->queue)) {
            return $this->factory->createHandler($this->queue[0]);
        }

        $next = clone $this;
        $middleware = \array_shift($next->queue);

        return $this->factory->createMiddleware($middleware, $next);
    }
}
