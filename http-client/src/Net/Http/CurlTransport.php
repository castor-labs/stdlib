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
use Castor\Bytes;
use Castor\Context;
use Castor\Io;
use Castor\Io\NoopCloser;
use Castor\Str;

final class CurlTransport implements Transport
{
    private static ?CurlTransport $instance = null;

    /**
     * @var \CurlHandle[]
     */
    private array $handles;

    public function __construct(
        private readonly bool $followRedirects = true,
        private readonly int $maxRedirects = 10,
        private readonly string $proxy = '',
        private readonly int $timeout = 0,
        private readonly bool $sslVerify = true,
        private readonly int $maxHandles = 5,
        private readonly array $customOptions = [],
        private readonly bool $exposeCurlInfo = false,
    ) {
        $this->handles = [];
    }

    public static function default(): CurlTransport
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function send(Context $ctx, Request $request): Response
    {
        $handle = $this->createHandle($request);
        $buffer = Io\Stream::memory('');

        $response = $this->prepare($handle, $request, $buffer);

        $curlInfo = null;

        try {
            curl_exec($handle);
            $this->checkError($request, $handle, curl_errno($handle));

            if ($this->exposeCurlInfo) {
                $curlInfo = curl_getinfo($handle);
            }
        } finally {
            $this->releaseHandle($handle);
        }

        if (null !== $curlInfo) {
            $response->headers->add('__curl_info', serialize($curlInfo));
        }

        $buffer->seek(0, Io\SEEK_START);
        $response->body = $buffer;

        return $response;
    }

    /**
     * @throws TransportError
     */
    private function createHandle(Request $request): \CurlHandle
    {
        $handle = [] !== $this->handles ? array_pop($this->handles) : curl_init();
        if (false === $handle) {
            throw new TransportError($request, 'Could not create curl handle', 0);
        }

        return $handle;
    }

    private function prepare(\CurlHandle $handle, Request $request, Io\Stream $buffer): Response
    {
        if (\defined('CURLOPT_PROTOCOLS')) {
            curl_setopt($handle, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
            curl_setopt($handle, CURLOPT_REDIR_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
        }

        curl_setopt($handle, CURLOPT_HEADER, false);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($handle, CURLOPT_FAILONERROR, false);

        $this->setOptionsFromTransport($handle);
        $this->setOptionsFromRequest($handle, $request);

        $response = Response::create();

        curl_setopt($handle, CURLOPT_HEADERFUNCTION, static function (\CurlHandle $ch, string $data) use ($response) {
            $str = Str\trim($data);
            if ('' !== $str) {
                if (0 === Str\index(Str\toLower($str), 'http/')) {
                    $parts = Str\split($str, ' ', 3);
                    $response->version = Version::from(Str\toUpper($parts[0]));
                    $response->status = Status::make((int) ($parts[1] ?? '200'), $parts[2] ?? '');

                    return Bytes\len($data);
                }

                $parts = Arr\map(Str\split($str, ':', 2), Str\trim(...));
                $response->headers->add($parts[0] ?? '', $parts[1] ?? '');
            }

            return Bytes\len($data);
        });

        curl_setopt($handle, CURLOPT_WRITEFUNCTION, static function (\CurlHandle $ch, string $data) use ($buffer) {
            return $buffer->write($data);
        });

        // Apply custom options
        if ([] !== $this->customOptions) {
            curl_setopt_array($handle, $this->customOptions);
        }

        return $response;
    }

    private function setOptionsFromRequest(\CurlHandle $handle, Request $request): void
    {
        $options = [
            CURLOPT_CUSTOMREQUEST => $request->method->value,
            CURLOPT_URL => $request->uri->toString(),
            CURLOPT_HTTPHEADER => $this->convertHeaders($request->headers),
        ];

        if (0 !== $version = $this->getProtocolVersion($request)) {
            $options[CURLOPT_HTTP_VERSION] = $version;
        }

        if ('' !== $request->uri->getUserInfo()) {
            $options[CURLOPT_USERPWD] = $request->uri->getUserInfo();
        }

        switch ($request->method) {
            case Method::HEAD:
                $options[CURLOPT_NOBODY] = true;

                break;

            case Method::GET:
                $options[CURLOPT_HTTPGET] = true;

                break;

            case Method::POST:
            case Method::PUT:
            case Method::DELETE:
            case Method::PATCH:
            case Method::OPTIONS:
                $body = $request->body;
                $bodySize = $this->getBodySize($body);

                // If the body size could not be determined or is too big, we stream it.
                if (0 === $bodySize || $bodySize > 1024 * 1024) {
                    $options[CURLOPT_UPLOAD] = true;
                    if (0 !== $bodySize) {
                        $options[CURLOPT_INFILESIZE] = $bodySize;
                    }
                    $options[CURLOPT_READFUNCTION] = static function ($ch, $fd, $length) use ($body) {
                        try {
                            return $body->read($length);
                        } catch (Io\EndOfFile) {
                            return '';
                        }
                    };

                    break;
                }

                // Small body can be loaded into memory
                $options[CURLOPT_POSTFIELDS] = Io\readAll($body);

                break;
        }

        curl_setopt_array($handle, $options);
    }

    /**
     * @return string[]
     */
    private function convertHeaders(Headers $headers): array
    {
        $curlHeaders = [];
        foreach ($headers as $key => $value) {
            $curlHeaders[] = Str\format('%s: %s', $key, $value);
        }

        return $curlHeaders;
    }

    private function getProtocolVersion(Request $request): int
    {
        return match ($request->version) {
            Version::HTTP10 => CURL_HTTP_VERSION_1_0,
            Version::HTTP11 => CURL_HTTP_VERSION_1_1,
            Version::HTTP20 => CURL_HTTP_VERSION_2_0
        };
    }

    private function getBodySize(Io\Reader $body): int
    {
        if ($body instanceof NoopCloser) {
            $body = $body->getReader();
        }

        // We can only determine the size of a seekable body
        if ($body instanceof Io\Seeker) {
            $bytes = $body->seek(0, Io\SEEK_END);
            $body->seek(0, Io\SEEK_START);

            return $bytes;
        }

        return 0;
    }

    private function setOptionsFromTransport(\CurlHandle $handle): void
    {
        if ('' !== $this->proxy) {
            curl_setopt($handle, CURLOPT_PROXY, $this->proxy);
        }

        $canFollow = $this->followRedirects;
        curl_setopt($handle, CURLOPT_FOLLOWLOCATION, $canFollow);
        curl_setopt($handle, CURLOPT_MAXREDIRS, $canFollow ? $this->maxRedirects : 0);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, $this->sslVerify ? 1 : 0);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, $this->sslVerify ? 2 : 0);
        if ($this->timeout > 0) {
            curl_setopt($handle, CURLOPT_TIMEOUT, $this->timeout);
        }
    }

    /**
     * @throws TransportError
     */
    private function checkError(Request $request, \CurlHandle $handle, int $error): void
    {
        switch ($error) {
            case CURLE_OK:
                // All OK, create a response object
                break;

            case CURLE_COULDNT_RESOLVE_PROXY:
            case CURLE_COULDNT_RESOLVE_HOST:
            case CURLE_COULDNT_CONNECT:
            case CURLE_OPERATION_TIMEOUTED:
            case CURLE_SSL_CONNECT_ERROR:
                throw new TransportError($request, curl_error($handle), $error);

            case CURLE_ABORTED_BY_CALLBACK:
                throw new TransportError($request, curl_error($handle), $error);

            default:
                throw new TransportError($request, curl_error($handle), $error);
        }
    }

    /**
     * Release a cUrl resource. This function is from Guzzle.
     */
    private function releaseHandle(\CurlHandle $handle): void
    {
        if (\count($this->handles) >= $this->maxHandles) {
            curl_close($handle);
        } else {
            // Remove all callback functions as they can hold onto references
            // and are not cleaned up by curl_reset. Using curl_setopt_array
            // does not work for some reason, so removing each one
            // individually.
            curl_setopt($handle, CURLOPT_HEADERFUNCTION, null);
            curl_setopt($handle, CURLOPT_READFUNCTION, null);
            curl_setopt($handle, CURLOPT_WRITEFUNCTION, null);
            curl_setopt($handle, CURLOPT_PROGRESSFUNCTION, null);
            curl_reset($handle);

            if (!\in_array($handle, $this->handles, true)) {
                $this->handles[] = $handle;
            }
        }
    }
}
