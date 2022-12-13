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

namespace Castor\Debug\Logger\Timer;

use Castor\Debug\Logger\Timer;
use Castor\Time;
use Castor\Time\Clock;

final class Monotonic implements Timer
{
    private Time $start;
    private Clock $clock;

    public function __construct(Time $start, Clock $clock)
    {
        $this->start = $start;
        $this->clock = $clock;
    }

    public static function now(Clock $clock = null): Monotonic
    {
        $clock = $clock ?? Clock\System::global();

        return new self($clock->now(), $clock);
    }

    public function time(): string
    {
        $start = $this->start->getTimestamp();
        $now = $this->clock->now()->getTimestamp();

        $offset = $now - $start;

        return str_pad((string) $offset, 10, '0', STR_PAD_LEFT);
    }
}
