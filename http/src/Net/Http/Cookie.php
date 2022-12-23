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
use Castor\Str;

class Cookie
{
    public function __construct(
        public string $name,
        public string $value = '',
        public string $path = '',
        public string $domain = '',
        public ?\DateTimeImmutable $expires = null,
        public int $maxAge = 0,
        public bool $secure = false,
        public bool $httpOnly = false,
        public SameSite $sameSite = SameSite::NONE,
    ) {
    }

    public function expire(): void
    {
        $this->expires = new \DateTimeImmutable('-5 years');
    }

    public function rememberForever(): void
    {
        $this->expires = new \DateTimeImmutable('+5 years');
    }

    /**
     * @return Cookie[]
     */
    public static function fromCookieString(string $string): array
    {
        return Arr\map(self::splitOnAttrDelimiter($string), static function (string $pair) {
            return static::fromCookiePair($pair);
        });
    }

    public static function fromSetCookieString(string $string): Cookie
    {
        $rawAttributes = self::splitOnAttrDelimiter($string);
        $rawAttribute = array_shift($rawAttributes);

        if (!is_string($rawAttribute)) {
            throw new \InvalidArgumentException(sprintf(
                'The provided cookie string "%s" must have at least one attribute',
                $string
            ));
        }

        [$name, $value] = self::splitOnAttrDelimiter($rawAttribute);

        $cookie = new static($name, $value);

        while ($rawAttribute = array_shift($rawAttributes)) {
            $rawAttributePair = explode('=', $rawAttribute, 2);

            $attributeKey = $rawAttributePair[0];
            $attributeValue = count($rawAttributePair) > 1 ? $rawAttributePair[1] : null;

            $attributeKey = strtolower($attributeKey);

            switch ($attributeKey) {
                case 'expires':
                    $cookie->expires = \DateTimeImmutable::createFromFormat('D, d M Y H:i:s T', $attributeValue);

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
                    $cookie->sameSite = SameSite::tryFrom((string) $attributeValue) ?? SameSite::NONE;

                    break;
            }
        }

        return $cookie;
    }

    public static function fromCookiePair(string $pair): Cookie
    {
        [$name, $value] = self::splitOnAttrDelimiter($pair);

        return new Cookie(
            $name,
            $value
        );
    }

    public function toCookieString(): string
    {
        return \urlencode($this->name).'='.\urlencode($this->value);
    }

    public function toSetCookieString(): string
    {
        $parts = [
            urlencode($this->name).'='.urlencode($this->value),
        ];

        if ('' !== $this->domain) {
            $parts[] = 'Domain='.$this->domain;
        }

        if ('' !== $this->path) {
            $parts[] = 'Path='.$this->path;
        }

        if (null !== $this->expires) {
            $date = $this->expires->setTimezone(new \DateTimeZone('UTC'))->format('D, d M Y H:i:s T');
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

        if ($this->sameSite) {
            $parts[] = 'SameSite='.$this->sameSite->value;
        }

        return Str\join($parts, '; ');
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

    /**
     * @return array{0: string, 1: string}
     */
    private static function splitCookiePair(string $pair): array
    {
        [$name, $value] = Str\cut($pair, '=');
        $value = \urldecode($value);

        return [$name, $value];
    }
}
