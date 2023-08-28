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

namespace Castor\Console\Command;

use Castor\Console\Printer;

abstract class Flag
{
    public function __construct(
        public readonly string $name,
        public readonly string $short = '',
        public readonly string $description = '',
        public readonly string $explanation = '',
    ) {
    }

    abstract public function consume(string $value): void;

    abstract public function getValue(): mixed;

    public function printUsage(Printer $printer): void
    {
        $printer->write('%s %s%s', $this->getDefinition(), "\t", $this->description);
        if ('' !== $this->explanation) {
            $printer->write($this->explanation);
        }
    }

    abstract protected function getValueName(): string;

    protected function getDefinition(): string
    {
        $parts = [];
        if ('' !== $this->short) {
            $parts[] = '-'.$this->short;
        }
        $parts[] = '--'.$this->name;

        $def = \implode(', ', $parts);
        $val = $this->getValueName();
        if ('' !== $val) {
            $def .= ' '.$val;
        }

        return $def;
    }
}
