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

class ProtocolError extends \Exception
{
    private Request $request;
    private Response $response;

    public function __construct(string $message, int $code, Request $request, Response $response)
    {
        parent::__construct($message, $code);
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @throws ProtocolError if the response is an error response
     */
    public static function check(Request $request, Response $response): void
    {
        if ($response->status->isSuccess()) {
            return;
        }

        $message = 'unexpected status code error';

        if ($response->status->isServerError()) {
            $message = \sprintf(
                'Server (%s) error on %s %s',
                $response->status->value,
                $request->method->value,
                $request->uri->toString()
            );
        }

        if ($response->status->isClientError()) {
            $message = \sprintf(
                'Client (%s) error on %s %s',
                $response->status->value,
                $request->method->value,
                $request->uri->toString()
            );
        }

        if ($response->status->isRedirect()) {
            $message = \sprintf(
                'Unexpected redirect (%s) status on %s %s',
                $response->status->value,
                $request->method->value,
                $request->uri->toString()
            );
        }

        throw new ProtocolError($message, $response->status->value, $request, $response);
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }
}
