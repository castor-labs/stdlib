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

use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\ParamProviders;
use PhpBench\Benchmark\Metadata\Annotations\Revs;

class StringInterpolationBench
{
    private const TEMPLATE = '%s %s';

    /**
     * @Revs(1000)
     *
     * @Iterations(5)
     *
     * @ParamProviders("provideStrings")
     */
    public function benchSprintf(array $params): void
    {
        \sprintf(self::TEMPLATE, $params[0], $params[1]);
    }

    /**
     * @Revs(1000)
     *
     * @Iterations(5)
     *
     * @ParamProviders("provideStrings")
     */
    public function benchInterpolation(array $params): void
    {
        "{$params[0]} {$params[1]}";
    }

    public function provideStrings(): \Generator
    {
        yield ['Hello', 'World!'];
    }
}
