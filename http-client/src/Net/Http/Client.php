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

class Client
{
    private static ?Client $default = null;

    public function __construct(
        private readonly Transport $transport
    ) {
    }

    public static function default(): Client
    {
        if (null === self::$default) {
            self::$default = new self(StreamTransport::default());
        }

        return self::$default;
    }

    /**
     * Sends the request and obtains a response.
     *
     * @throws TransportError if an error in the network stack occurs
     */
    public function send(Context $ctx, Request $request): Response
    {
        return $this->transport->send($ctx, $request);
    }

    /**
     * Sends an HTTP Request in strict mode.
     *
     * Strict mode throws a HTTPError when a non-successful HTTP status code
     * is returned from the server. The exception contains the response for
     * further inspection.
     *
     * @throws ProtocolError  if a non-successful status code is returned
     * @throws TransportError if an error in the network stack occurs
     */
    public function sendStrict(Context $ctx, Request $request): Response
    {
        $response = $this->send($ctx, $request);
        ProtocolError::check($request, $response);

        return $response;
    }
}
