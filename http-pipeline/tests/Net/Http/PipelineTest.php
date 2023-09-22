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

namespace Castor\Net\Http\tests\Net\Http;

use Castor\Context;
use Castor\Net\Http\Handler;
use Castor\Net\Http\Recorder;
use Castor\Net\Http\Request;
use Http\Pipeline;
use Http\Pipeline\HandlerFactory;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class PipelineTest extends TestCase
{
    public function testPipeline(): void
    {
        $handler = $this->createMock(Handler::class);
        $handlerFactory = $this->createMock(HandlerFactory::class);

        $handlerFactory->expects($this->exactly(3))
            ->method('createMiddleware')
            ->withConsecutive(
                ['middleware_1', $this->isInstanceOf(Handler::class)],
                ['middleware_2', $this->isInstanceOf(Handler::class)],
                ['middleware_3', $this->isInstanceOf(Handler::class)]
            )->willReturnCallback(function (string $name, Handler $next): Handler {
                return $next;
            })
        ;

        $handlerFactory->expects($this->once())
            ->method('createHandler')
            ->with('final_handler')
            ->willReturn($handler)
        ;

        $handler->expects($this->once())
            ->method('handle')
        ;

        $pipeline = new Pipeline($handlerFactory, [
            'middleware_1',
            'middleware_2',
            'middleware_3',
            'final_handler',
        ]);

        $pipeline->handle(Context\nil(), Request::create('GET', '/hello'), Recorder::create());
    }
}
