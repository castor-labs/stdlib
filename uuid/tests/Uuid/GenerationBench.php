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

namespace Castor\Uuid;

use Ramsey\Uuid\Uuid;

class GenerationBench
{
    /**
     * @Revs(100)
     *
     * @Iterations(7)
     */
    public function benchV1Ramsey100(): void
    {
        for ($i = 0; $i < 100; ++$i) {
            $v1 = Uuid::uuid1()->toString();
        }
    }

    /**
     * @Revs(1000)
     *
     * @Iterations(20)
     */
    public function benchV1RamseyOne(): void
    {
        $v1 = Uuid::uuid1()->toString();
    }

    /**
     * @Revs(100)
     *
     * @Iterations(7)
     */
    public function benchV1Castor100(): void
    {
        for ($i = 0; $i < 100; ++$i) {
            $v1 = V1::generate()->toString();
        }
    }

    /**
     * @Revs(1000)
     *
     * @Iterations(20)
     */
    public function benchV1CastorOne(): void
    {
        $v1 = V1::generate()->toString();
    }

    /**
     * @Revs(100)
     *
     * @Iterations(7)
     */
    public function benchV4Ramsey100(): void
    {
        for ($i = 0; $i < 100; ++$i) {
            $v4 = Uuid::uuid4()->toString();
        }
    }

    /**
     * @Revs(1000)
     *
     * @Iterations(20)
     */
    public function benchV4RamseyOne(): void
    {
        $v4 = Uuid::uuid4()->toString();
    }

    /**
     * @Revs(100)
     *
     * @Iterations(7)
     */
    public function benchV4Castor100(): void
    {
        for ($i = 0; $i < 100; ++$i) {
            $v4 = V4::generate()->toString();
        }
    }

    /**
     * @Revs(1000)
     *
     * @Iterations(20)
     */
    public function benchV4CastorOne(): void
    {
        $v4 = V4::generate()->toString();
    }

    /**
     * @Revs(10000)
     *
     * @Iterations(5)
     */
    public function benchParseV1Ramsey(): void
    {
        $uuid = Uuid::fromString('1b4a5100-478a-11ee-be56-0242ac120002');
    }

    /**
     * @Revs(10000)
     *
     * @Iterations(5)
     */
    public function benchParseV1Castor(): void
    {
        $uuid = \parse('1b4a5100-478a-11ee-be56-0242ac120002');
    }
}
