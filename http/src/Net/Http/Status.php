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

class Status
{
    public const CONTINUE = 100;
    public const SWITCHING_PROTOCOLS = 101;
    public const PROCESSING = 102;
    public const EARLY_HINTS = 103;
    public const OK = 200; // RFC 7231, 6.3.1
    public const CREATED = 201; // RFC 7231, 6.3.2
    public const ACCEPTED = 202; // RFC 7231, 6.3.3
    public const NON_AUTHORITATIVE_INFO = 203; // RFC 7231, 6.3.4
    public const NO_CONTENT = 204; // RFC 7231, 6.3.5
    public const RESET_CONTENT = 205; // RFC 7231, 6.3.6
    public const PARTIAL_CONTEXT = 206; // RFC 7233, 4.1
    public const MULTI_STATUS = 207; // RFC 4918, 11.1
    public const ALREADY_REPORTED = 208; // RFC 5842, 7.1
    public const IM_USED = 226; // RFC 3229, 10.4.1
    public const MULTIPLE_CHOICES = 300; // RFC 7231, 6.4.1
    public const MOVED_PERMANENTLY = 301; // RFC 7231, 6.4.2
    public const FOUND = 302; // RFC 7231, 6.4.3
    public const SEE_OTHER = 303; // RFC 7231, 6.4.4
    public const NOT_MODIFIED = 304; // RFC 7232, 4.1
    public const USE_PROXY = 305; // RFC 7231, 6.4.5
    public const TEMPORARY_REDIRECT = 307; // RFC 7231, 6.4.7
    public const PERMANENT_REDIRECT = 308; // RFC 7538, 3
    public const BAD_REQUEST = 400; // RFC 7231, 6.5.1
    public const UNAUTHORIZED = 401; // RFC 7235, 3.1
    public const PAYMENT_REQUIRED = 402; // RFC 7231, 6.5.2
    public const FORBIDDEN = 403; // RFC 7231, 6.5.3
    public const NOT_FOUND = 404; // RFC 7231, 6.5.4
    public const METHOD_NOT_ALLOWED = 405; // RFC 7231, 6.5.5
    public const NOT_ACCEPTABLE = 406; // RFC 7231, 6.5.6
    public const PROXY_AUTH_REQUIRED = 407; // RFC 7235, 3.2
    public const REQUEST_TIMEOUT = 408; // RFC 7231, 6.5.7
    public const CONFLICT = 409; // RFC 7231, 6.5.8
    public const GONE = 410; // RFC 7231, 6.5.9
    public const LENGTH_REQUIRED = 411; // RFC 7231, 6.5.10
    public const PRECONDITION_FAILED = 412; // RFC 7232, 4.2
    public const REQUEST_ENTITY_TOO_LARGE = 413; // RFC 7231, 6.5.11
    public const REQUEST_URI_TOO_LONG = 414; // RFC 7231, 6.5.12
    public const UNSUPPORTED_MEDIA_TYPE = 415; // RFC 7231, 6.5.13
    public const REQUEST_RANGE_NOT_SATISFIABLE = 416; // RFC 7233, 4.4
    public const EXPECTATION_FAILED = 417; // RFC 7231, 6.5.14
    public const TEAPOT = 418; // RFC 7168, 2.3.3
    public const MISDIRECTED_REQUEST = 421; // RFC 7540, 9.1.2
    public const UNPROCESSABLE_ENTITY = 422; // RFC 4918, 11.2
    public const LOCKED = 423; // RFC 4918, 11.3
    public const FAILED_DEPENDENCY = 424; // RFC 4918, 11.4
    public const TOO_EARLY = 425; // RFC 8470, 5.2.
    public const UPGRADE_REQUIRED = 426; // RFC 7231, 6.5.15
    public const PRECONDITION_REQUIRED = 428; // RFC 6585, 3
    public const TOO_MANY_REQUESTS = 429; // RFC 6585, 4
    public const REQUEST_HEADER_FIELDS_TOO_LARGE = 431; // RFC 6585, 5
    public const UNAVAILABLE_FOR_LEGAL_REASONS = 451; // RFC 7725, 3
    public const INTERNAL_SERVER_ERROR = 500; // RFC 7231, 6.6.1
    public const NOT_IMPLEMENTED = 501; // RFC 7231, 6.6.2
    public const BAD_GATEWAY = 502; // RFC 7231, 6.6.3
    public const SERVICE_UNAVAILABLE = 503; // RFC 7231, 6.6.4
    public const GATEWAY_TIMEOUT = 504; // RFC 7231, 6.6.5
    public const HTTP_VERSION_NOT_SUPPORTED = 505; // RFC 7231, 6.6.6
    public const VARIANT_ALSO_NEGOTIATES = 506; // RFC 2295, 8.1
    public const INSUFFICIENT_STORAGE = 507; // RFC 4918, 11.5
    public const LOOP_DETECTED = 508; // RFC 5842, 7.2
    public const NOT_EXTENDED = 510; // RFC 2774, 7
    public const NETWORK_AUTHENTICATION_REQUIRED = 511; // RFC 6585, 6

    private static array $statusPhraseMap = [
        self::CONTINUE => 'Continue',
        self::SWITCHING_PROTOCOLS => 'Switching Protocols',
        self::PROCESSING => 'Processing',
        self::EARLY_HINTS => 'Early Hints',
        self::OK => 'OK',
        self::CREATED => 'Created',
        self::ACCEPTED => 'Accepted',
        self::NON_AUTHORITATIVE_INFO => 'Non-Authoritative Information',
        self::NO_CONTENT => 'No Content',
        self::RESET_CONTENT => 'Reset Content',
        self::PARTIAL_CONTEXT => 'Partial Content',
        self::MULTI_STATUS => 'Multi-Status',
        self::ALREADY_REPORTED => 'Already Reported',
        self::IM_USED => 'IM Used',
        self::MULTIPLE_CHOICES => 'Multiple Choices',
        self::MOVED_PERMANENTLY => 'Moved Permanently',
        self::FOUND => 'Found',
        self::SEE_OTHER => 'See Other',
        self::NOT_MODIFIED => 'Not Modified',
        self::USE_PROXY => 'Use Proxy',
        self::TEMPORARY_REDIRECT => 'Temporary Redirect',
        self::PERMANENT_REDIRECT => 'Permanent Redirect',
        self::BAD_REQUEST => 'Bad Request',
        self::UNAUTHORIZED => 'Unauthorized',
        self::PAYMENT_REQUIRED => 'Payment Required',
        self::FORBIDDEN => 'Forbidden',
        self::NOT_FOUND => 'Not Found',
        self::METHOD_NOT_ALLOWED => 'Method Not Allowed',
        self::NOT_ACCEPTABLE => 'Not Acceptable',
        self::PROXY_AUTH_REQUIRED => 'Proxy Authentication Required',
        self::REQUEST_TIMEOUT => 'Request Timeout',
        self::CONFLICT => 'Conflict',
        self::GONE => 'Gone',
        self::LENGTH_REQUIRED => 'Length Required',
        self::PRECONDITION_FAILED => 'Precondition Failed',
        self::REQUEST_ENTITY_TOO_LARGE => 'Request Entity Too Large',
        self::REQUEST_URI_TOO_LONG => 'Request Uri Too Long',
        self::UNSUPPORTED_MEDIA_TYPE => 'Unsupported Media Type',
        self::REQUEST_RANGE_NOT_SATISFIABLE => 'Request Range Not Satisfiable',
        self::EXPECTATION_FAILED => 'Expectation Failed',
        self::TEAPOT => 'I\'m a Teapot',
        self::MISDIRECTED_REQUEST => 'Misdirected Request',
        self::UNPROCESSABLE_ENTITY => 'Unprocessable Entity',
        self::LOCKED => 'Locked',
        self::FAILED_DEPENDENCY => 'Failed Dependency',
        self::TOO_EARLY => 'Too Early',
        self::UPGRADE_REQUIRED => 'Upgrade Required',
        self::PRECONDITION_REQUIRED => 'Precondition Required',
        self::TOO_MANY_REQUESTS => 'Too Many Requests',
        self::REQUEST_HEADER_FIELDS_TOO_LARGE => 'Request Header Fields Too Large',
        self::UNAVAILABLE_FOR_LEGAL_REASONS => 'Unavailable For Legal Reasons',
        self::INTERNAL_SERVER_ERROR => 'Internal Server Error',
        self::NOT_IMPLEMENTED => 'Not Implemented',
        self::BAD_GATEWAY => 'Bad Gateway',
        self::SERVICE_UNAVAILABLE => 'Service Unavailable',
        self::GATEWAY_TIMEOUT => 'Gateway Timeout',
        self::HTTP_VERSION_NOT_SUPPORTED => 'HTTP Version Not Supported',
        self::VARIANT_ALSO_NEGOTIATES => 'Variant Also Negotiates',
        self::INSUFFICIENT_STORAGE => 'Insufficient Storage',
        self::LOOP_DETECTED => 'Loop Detected',
        self::NOT_EXTENDED => 'Not Extended',
        self::NETWORK_AUTHENTICATION_REQUIRED => 'Network Authentication Error',
    ];

    public function __construct(
        public readonly int $value,
        public readonly string $phrase,
    ) {
    }

    public static function make(int $status, string $phrase = ''): Status
    {
        $phrase = \strtoupper($phrase);
        if ('' === $phrase) {
            $phrase = self::$statusPhraseMap[$status] ?? 'UNKNOWN';
        }

        return new self($status, $phrase);
    }

    public static function fromInt(int $status = Status::OK): Status
    {
        $phrase = self::$statusPhraseMap[$status] ?? 'Non Standard';

        return new self($status, \strtoupper($phrase));
    }

    public function isClientError(): bool
    {
        return $this->inRange(400, 499);
    }

    public function isServerError(): bool
    {
        return $this->inRange(500, 599);
    }

    public function isRedirect(): bool
    {
        return $this->inRange(300, 399);
    }

    public function isSuccess(): bool
    {
        return $this->inRange(200, 299);
    }

    public function inRange(int $a, int $b): bool
    {
        return $this->value >= $a && $this->value <= $b;
    }
}
