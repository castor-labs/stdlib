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

namespace Castor\Uuid\V1;

use Castor\Crypto\Bytes;

final class Fixed implements State
{
    public function __construct(
        private readonly GregorianTime $time,
        private readonly Bytes $clockSeq,
        private readonly Bytes $node,
    ) {
    }

    public function getClockSequence(): Bytes
    {
        return $this->clockSeq;
    }

    public function getTime(): GregorianTime
    {
        return $this->time;
    }

    public function getNode(): Bytes
    {
        return $this->node;
    }
}
