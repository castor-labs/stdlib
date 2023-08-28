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

namespace Castor\Uuid\System;

use Castor\Crypto\Random;
use Castor\Uuid\System\MacProvider\Fallback;
use Castor\Uuid\System\MacProvider\FromOs;

final class Defaults implements MacProvider, Clock
{
    private static ?Defaults $global = null;

    public function __construct(
        private readonly MacProvider $macProvider,
    ) {
    }

    public static function global(): Defaults
    {
        if (null === self::$global) {
            self::$global = new self(
                new FromOs(new Fallback(Random::global()))
            );
        }

        return self::$global;
    }

    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }

    public function getMacAddresses(): array
    {
        return $this->macProvider->getMacAddresses();
    }
}
