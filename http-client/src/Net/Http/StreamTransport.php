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

use Castor\Arr;
use Castor\Context;
use Castor\Io;
use Castor\Str;

final class StreamTransport implements Transport
{
    private static ?StreamTransport $instance = null;

    public function __construct(
        private readonly bool $followRedirects = true,
        private readonly string $proxy = '',
        private readonly int $maxRedirects = 10,
        private readonly float $timeout = -1,
        private readonly bool $sslVerify = true,
    ) {
    }

    public static function default(): StreamTransport
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @throws TransportError
     */
    public function send(Context $ctx, Request $request): Response
    {
        $context = [
            'http' => [
                'method' => $request->method->value,
                'header' => $this->convertHeaders($request->headers),
                'contents' => Io\readAll($request->body),
                'ignore_errors' => true,
                'follow_location' => $this->followRedirects ? 1 : 0,
                'max_redirects' => $this->maxRedirects,
                'protocol_version' => $request->version->toFloat(),
            ],
            'ssl' => [
                'verify_peer' => $this->sslVerify,
            ],
        ];

        if ($this->timeout >= 0) {
            $context['http']['timeout'] = $this->timeout;
        }

        if ('' !== $this->proxy) {
            $context['http']['proxy'] = $this->proxy;
            $context['http']['request_fulluri'] = true;
        }

        $resource = @fopen($request->uri->toString(), 'rb', false, stream_context_create($context));
        if (!is_resource($resource)) {
            throw new TransportError($request, error_get_last()['message'] ?? 'Unknown error', 0);
        }

        stream_set_blocking($resource, false);

        // We extract relevant stream meta data
        $meta = stream_get_meta_data($resource);

        $responses = $this->parseResponses($meta['wrapper_data'] ?? []);

        // Pick the last response and put the body there.
        $response = $responses[count($responses) - 1];

        $response->body = PhpResourceBody::from($resource);

        return $response;
    }

    /**
     * @return string[]
     */
    private function convertHeaders(Headers $headers): array
    {
        $streamHeaders = [];
        foreach ($headers as $key => $value) {
            $streamHeaders[] = Str\format('%s: %s', $key, $value);
        }

        return $streamHeaders;
    }

    /**
     * @param mixed $lines
     */
    private function parseResponses(array $lines): array
    {
        /** @var Response[] $responses */
        $responses = [];
        $current = 0;
        foreach ($lines as $line) {
            if (-1 !== Str\index(Str\toLower($line), 'http/')) {
                $response = Response::create();
                $parts = Str\split($line, ' ', 3);
                $response->version = Version::from(Str\toUpper($parts[0]));
                $response->status = Status::make((int) ($parts[1] ?? '200'), $parts[2] ?? '');
                $current = Arr\push($responses, $response) - 1;

                continue;
            }

            $parts = Arr\map(Str\split($line, ':', 2), Str\trim(...));
            $responses[$current]->headers->add($parts[0] ?? '', $parts[1] ?? '');
        }

        return $responses;
    }
}
