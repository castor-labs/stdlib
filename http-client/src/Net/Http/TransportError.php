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

/**
 * A TransportError represents an error that occurs in the network stack (DNS, TCP/IP, SSL).
 */
class TransportError extends \Exception
{
    private Request $request;

    public function __construct(Request $request, string $message, int $code, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->request = $request;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
}
