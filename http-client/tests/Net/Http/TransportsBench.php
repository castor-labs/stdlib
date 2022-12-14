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
use Castor\Net\Uri\ParseError;
use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\ParamProviders;
use PhpBench\Benchmark\Metadata\Annotations\Revs;
use PhpBench\Benchmark\Metadata\Annotations\Skip;

/**
 * @Skip()
 */
class TransportsBench
{
    /**
     * @throws TransportError
     *
     * @Revs(10)
     * @Iterations(5)
     * @ParamProviders("provideRequests")
     */
    public function benchStreamSend(array $params): void
    {
        $stream = StreamTransport::default();
        $stream->send($params[0], $params[1]);
    }

    /**
     * @throws TransportError
     *
     * @Revs(10)
     * @Iterations(5)
     * @ParamProviders("provideRequests")
     */
    public function benchCurlSend(array $params): void
    {
        $stream = CurlTransport::default();
        $stream->send($params[0], $params[1]);
    }

    /**
     * @throws ParseError
     */
    public function provideRequests(): \Generator
    {
        yield [Context\nil(), Request::create('GET', 'https://gpg.mnavarro.dev')];
    }
}
