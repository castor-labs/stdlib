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

namespace Castor;

class Time extends \DateTimeImmutable
{
    final protected function __construct(string $datetime = 'now', ?\DateTimeZone $timezone = null)
    {
        parent::__construct($datetime, $timezone);
    }

    /**
     * @return Time
     *
     * @throws \Exception if the parsing fails of the date or the timezone fails
     */
    public static function parse(string $format, string $value, string $tz = 'UTC'): static
    {
        $time = self::createFromFormat($format, $value, new \DateTimeZone($tz));
        if ($time instanceof self) {
            return $time;
        }

        throw Err\last();
    }

    /**
     * @throws \Exception if the guessing fails
     */
    public static function guess(string $value, string $tz = 'UTC'): static
    {
        return new self($value, new \DateTimeZone($tz));
    }

    public static function now(string $tz = 'UTC'): static
    {
        try {
            return new self('now', new \DateTimeZone($tz));
        } catch (\Exception $e) {
            throw new \RuntimeException('Error creating now time', 0, $e);
        }
    }
}
