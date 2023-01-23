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
use Castor\Encoding\Url;
use Castor\Str;

/**
 * Class Cookie represents a Cookie as defined in RFC 6265.
 *
 * @psalm-external-mutation-free
 */
class Cookie
{
    public const EXPIRES_DATE_FORMAT = 'D, d M Y H:i:s T';

    final public function __construct(
        public string $name,
        public string $value = '',
        public string $path = '',
        public string $domain = '',
        public ?\DateTimeImmutable $expires = null,
        public int $maxAge = 0,
        public bool $secure = false,
        public bool $httpOnly = false,
        public ?SameSite $sameSite = null,
    ) {
    }

    public function expire(): void
    {
        $this->expires = new \DateTimeImmutable('-5 years');
        $this->maxAge = 0; // Ignore previously set Max-Age
    }

    public function rememberForever(): void
    {
        $this->expires = new \DateTimeImmutable('+5 years');
        $this->maxAge = 0; // Ignore previous set Max-Age
    }

    /**
     * Creates a list of Cookies from a Cookie header string.
     *
     * @return static[]
     */
    final public static function fromCookieString(string $string): array
    {
        return Arr\map(static::splitOnAttrDelimiter($string), static function (string $pair) {
            return static::fromCookiePair($pair);
        });
    }

    /**
     * Creates a Cookie from Set-Cookie header string.
     *
     * If the cookie is malformed, then an empty cookie with name as an empty string is returned.
     *
     * If the Expires date or the SameSite attributes are invalid, those are silently skipped and ommited.
     */
    final public static function fromSetCookieString(string $string): static
    {
        $rawAttributes = static::splitOnAttrDelimiter($string);
        $firstAttribute = Arr\shift($rawAttributes);

        if (!\is_string($firstAttribute)) {
            return new static('');
        }

        [$name, $value] = static::splitNameValuePair($firstAttribute);

        $cookie = new static($name, $value);

        while ($rawAttribute = Arr\shift($rawAttributes)) {
            $rawAttributePair = Str\split($rawAttribute, '=', 2);

            $attributeKey = $rawAttributePair[0];
            $attributeValue = \count($rawAttributePair) > 1 ? $rawAttributePair[1] : null;

            $attributeKey = Str\toLower($attributeKey);

            switch ($attributeKey) {
                case 'expires':
                    $datetime = \DateTimeImmutable::createFromFormat(self::EXPIRES_DATE_FORMAT, $attributeValue);
                    if ($datetime instanceof \DateTimeImmutable) {
                        $cookie->expires = $datetime;
                    }

                    break;

                case 'max-age':
                    $cookie->maxAge = (int) $attributeValue;

                    break;

                case 'domain':
                    $cookie->domain = $attributeValue;

                    break;

                case 'path':
                    $cookie->path = $attributeValue;

                    break;

                case 'secure':
                    $cookie->secure = true;

                    break;

                case 'httponly':
                    $cookie->httpOnly = true;

                    break;

                case 'samesite':
                    $cookie->sameSite = SameSite::tryFrom((string) $attributeValue);

                    break;
            }
        }

        return $cookie;
    }

    final public static function fromCookiePair(string $pair): static
    {
        [$name, $value] = static::splitNameValuePair($pair);

        return new Cookie(
            $name,
            $value
        );
    }

    /**
     * Creates a new cookie based on the values of this one.
     */
    public function copy(): static
    {
        return clone $this;
    }

    /**
     * Returns the cookie as a string for use on a Cookie header.
     *
     * If the cookie name is empty, then this method returns an empty string
     */
    public function toCookieString(): string
    {
        if ('' === $this->name) {
            return '';
        }

        return Url\encode($this->name).'='.Url\encode($this->value);
    }

    /**
     * Returns the cookie as a string for use on a Set-Cookie header.
     *
     * If the cookie name is empty, this method returns an empty string
     */
    public function toSetCookieString(): string
    {
        if ('' === $this->name) {
            return '';
        }

        $parts = [
            Url\encode($this->name).'='.Url\encode($this->value),
        ];

        if ('' !== $this->domain) {
            $parts[] = 'Domain='.$this->domain;
        }

        if ('' !== $this->path) {
            $parts[] = 'Path='.$this->path;
        }

        if (null !== $this->expires) {
            $date = $this->expires
                ->setTimezone(new \DateTimeZone('UTC'))
                ->format(self::EXPIRES_DATE_FORMAT)
            ;
            $parts[] = 'Expires='.$date;
        }

        if (0 !== $this->maxAge) {
            $parts[] = 'Max-Age='.$this->maxAge;
        }

        if ($this->secure) {
            $parts[] = 'Secure';
        }

        if ($this->httpOnly) {
            $parts[] = 'HttpOnly';
        }

        if (null !== $this->sameSite) {
            $parts[] = 'SameSite='.$this->sameSite->value;
        }

        return Str\join($parts, '; ');
    }

    /**
     * @return array{0: string, 1: string}
     */
    private static function splitNameValuePair(string $pair): array
    {
        [$name, $value] = Str\cut($pair, '=');
        $value = Url\decode($value);

        return [$name, $value];
    }

    /**
     * @return string[]
     */
    private static function splitOnAttrDelimiter(string $string): array
    {
        $splitAttributes = \preg_split('@\s*[;]\s*@', $string);
        if (!\is_array($splitAttributes)) {
            $splitAttributes = []; // Malformed cookie header should be ignored
        }

        return Arr\filter($splitAttributes);
    }
}
