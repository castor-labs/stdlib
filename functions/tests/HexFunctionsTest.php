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

use Castor\Encoding\EncodingInputError;
use Castor\Encoding\Hex;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class HexFunctionsTest extends TestCase
{
    public function testEncode(): void
    {
        $input = 'FooBar';
        $output = Hex\encode($input);

        $this->assertSame('466f6f426172', $output);
    }

    /**
     * @throws EncodingInputError
     */
    public function testDecode(): void
    {
        $input = '466f6f426172';
        $output = Hex\decode($input);

        $this->assertSame('FooBar', $output);
    }

    public function testDecodeError(): void
    {
        $input = 'not a hex string';

        $this->expectException(EncodingInputError::class);
        $this->expectErrorMessage('Input string must be hexadecimal string');
        Hex\decode($input);
    }
}
