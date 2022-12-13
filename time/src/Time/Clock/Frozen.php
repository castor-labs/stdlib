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

final class Frozen implements Clock
{
    private Time $start;
    private Time $current;

    public function __construct(Time $start)
    {
        $this->start = $start;
        $this->current = $start;
    }

    /**
     * @throws \Exception if there is an error parsing the time
     */
    public static function at(string $format, string $value, string $tz = 'UTC'): Frozen
    {
        return new self(Time::parse($format, $value, $tz));
    }

    public function advance(string $interval): void
    {
        try {
            $i = new \DateInterval($interval);
        } catch (\Exception $e) {
            throw new \RuntimeException('Wrong interval', 0, $e);
        }

        $this->current = $this->current->add($i);
    }

    public function rewind(string $interval): void
    {
        try {
            $i = new \DateInterval($interval);
        } catch (\Exception $e) {
            throw new \RuntimeException('Wrong interval', 0, $e);
        }

        $this->current = $this->current->sub($i);
    }

    public function reset(): void
    {
        $this->current = $this->start;
    }

    public function now(): Time
    {
        return $this->current;
    }
}
