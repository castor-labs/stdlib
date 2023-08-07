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

namespace Castor\Debug\Logger\Colorizer;

use Castor\Debug\Logger\Colorizer;
use Castor\Debug\Logger\Level;

final class Ansi implements Colorizer
{
    private const RED = 0;
    private const GREEN = 1;
    private const YELLOW = 2;
    private const BLUE = 3;

    /**
     * @var array<int, array{0: string, 1: string}>
     */
    private static array $colorMap = [
        ["\033[31m", "\033[0m"],
        ["\033[32m", "\033[0m"],
        ["\033[33m", "\033[0m"],
        ["\033[36m", "\033[0m"],
    ];

    public function colorize(Level $level, string $text): string
    {
        $color = $this->getColorFor($level);
        if ([] === $color) {
            return $text;
        }

        return $color[0].$text.$color[1];
    }

    /**
     * @return array{0: string, 1: string}
     */
    public function getColorFor(Level $level): array
    {
        return match ($level) {
            Level::FATAL, Level::ERROR => self::$colorMap[self::RED],
            Level::WARN => self::$colorMap[self::YELLOW],
            Level::INFO => self::$colorMap[self::BLUE],
            Level::DEBUG, Level::TRACE => self::$colorMap[self::GREEN],
        };
    }
}
