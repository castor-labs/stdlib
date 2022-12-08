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

namespace Castor\Net\Uri;

class UserInfo
{
    public function __construct(
        private readonly string $user = '',
        private readonly ?string $pass = null,
    ) {
    }

    public static function create(string $user, ?string $pass = null): UserInfo
    {
        return new self(rawurldecode($user), null !== $pass ? rawurldecode($pass) : null);
    }

    public static function parse(string $string): UserInfo
    {
        $i = strpos($string, ':');
        if (!is_int($i)) {
            return self::create($string);
        }

        return self::create(
            substr($string, 0, $i),
            substr($string, $i + 1),
        );
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getPass(): ?string
    {
        return $this->pass;
    }

    public function toString(): string
    {
        $userInfo = $this->user;
        if (null !== $this->pass) {
            $userInfo .= ':'.$this->pass;
        }

        return $userInfo;
    }

    public function encode(): string
    {
        $userInfo = rawurlencode($this->user);
        if (null !== $this->pass) {
            $userInfo .= ':'.rawurlencode($this->pass);
        }

        return $userInfo;
    }
}
