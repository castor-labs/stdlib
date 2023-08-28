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

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \Castor\Uuid\V5
 */
class V5Test extends TestCase
{
    public function testCreate(): void
    {
        $v4 = V4::parse('7628f4de-01bd-494b-a84b-d7f900521218');
        $v5 = V5::create($v4, 'test');
        $this->assertSame('5fe80e27-269a-5cce-98c3-989ddd181b71', $v5->toString());
    }

    /**
     * @dataProvider getParseErrorData
     */
    public function testParseError(string $in, string $message): void
    {
        try {
            V5::parse($in);
        } catch (ParsingError $e) {
            $this->assertSame($message, $e->getMessage());
        }
    }

    public static function getParseErrorData(): array
    {
        return [
            'less chars' => ['00010f-0405-4607-8809-0a0b0c0d0e0f', 'UUID must have 16 bytes.'],
            'more chars' => ['0001003f-0405-4607-8809-0a0b0c0d0e0fdf', 'UUID must have 16 bytes.'],
            'wrong version' => ['7628f4de-01bd-494b-a84b-d7f900521218', 'Not a valid version 5 UUID.'],
            'invalid hex' => ['ZZlf8060-4a64-4f50-9e10-882cb74461f7', 'Invalid hexadecimal in UUID.'],
        ];
    }

    /**
     * @throws \JsonException
     */
    public function testSerialization(): void
    {
        $v5 = V5::parse('5fe80e27-269a-5cce-98c3-989ddd181b71');

        $serialized = \serialize($v5);
        $json = \json_encode(['uuid' => $v5], JSON_THROW_ON_ERROR);

        $this->assertSame('O:14:"Castor\Uuid\V5":1:{i:0;s:36:"5fe80e27-269a-5cce-98c3-989ddd181b71";}', $serialized);
        $this->assertTrue($v5->equals(\unserialize($serialized)));
        $this->assertSame('{"uuid":"5fe80e27-269a-5cce-98c3-989ddd181b71"}', $json);
        $this->assertSame('5fe80e27-269a-5cce-98c3-989ddd181b71', (string) $v5);
    }
}
