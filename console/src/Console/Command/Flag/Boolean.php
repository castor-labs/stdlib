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

namespace Castor\Console\Command\Flag;

use Castor\Console\Command\Flag;

class Boolean extends Flag
{
    public function __construct(
        string $name,
        string $short = '',
        string $description = '',
        string $explanation = '',
        private bool $value = false,
    ) {
        parent::__construct($name, $short, $description, $explanation);
    }

    public function consume(string $value): void
    {
        if ('' === $value) {
            $this->value = true;

            return;
        }

        if ('1' === $value || 'true' === $value) {
            $this->value = true;
        }
    }

    public function getValue(): bool
    {
        return $this->value;
    }

    protected function getValueName(): string
    {
        return '';
    }
}
