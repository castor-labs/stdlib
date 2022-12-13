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

namespace Castor\Time\Clock;

use Castor\Time;
use Castor\Time\Clock;

final class System implements Clock
{
    private static ?System $global = null;

    private string $tz;

    public function __construct(string $tz = 'UTC')
    {
        $this->tz = $tz;
    }

    /**
     * Returns the global system clock instance.
     *
     * @note Use this method only in bootstrapping code
     */
    public static function global(): System
    {
        if (null === self::$global) {
            self::$global = new System();
        }

        return self::$global;
    }

    public function now(): Time
    {
        return Time::now($this->tz);
    }
}
