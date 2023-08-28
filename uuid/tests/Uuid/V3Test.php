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
 * @covers \Castor\Uuid\V3
 */
class V3Test extends TestCase
{
    public function testCreate(): void
    {
        $v4 = V4::parse('7628f4de-01bd-494b-a84b-d7f900521218');
        $v3 = V3::create($v4, 'test');
        $this->assertSame('a0f6aad0-cdf5-3ddc-a2ac-0bddb3249309', $v3->toString());
    }

    /**
     * @dataProvider getParseErrorData
     */
    public function testParseError(string $in, string $message): void
    {
        try {
            V3::parse($in);
        } catch (ParsingError $e) {
            $this->assertSame($message, $e->getMessage());
        }
    }

    public static function getParseErrorData(): array
    {
        return [
            'less chars' => ['00010f-0405-4607-8809-0a0b0c0d0e0f', 'UUID must have 16 bytes.'],
            'more chars' => ['00010203-0405-4607-8809-0a0b0c0d0e0fdf', 'UUID must have 16 bytes.'],
            'wrong version' => ['7628f4de-01bd-494b-a84b-d7f900521218', 'Not a valid version 3 UUID.'],
            'invalid hex' => ['ZZlf8060-4a64-4f50-9e10-882cb74461f7', 'Invalid hexadecimal in UUID.'],
        ];
    }

    /**
     * @throws \JsonException
     */
    public function testSerialization(): void
    {
        $v3 = V3::parse('a0f6aad0-cdf5-3ddc-a2ac-0bddb3249309');

        $serialized = \serialize($v3);
        $json = \json_encode(['uuid' => $v3], JSON_THROW_ON_ERROR);

        $this->assertSame('O:14:"Castor\Uuid\V3":1:{i:0;s:36:"a0f6aad0-cdf5-3ddc-a2ac-0bddb3249309";}', $serialized);
        $this->assertTrue($v3->equals(\unserialize($serialized)));
        $this->assertSame('{"uuid":"a0f6aad0-cdf5-3ddc-a2ac-0bddb3249309"}', $json);
        $this->assertSame('a0f6aad0-cdf5-3ddc-a2ac-0bddb3249309', (string) $v3);
    }
}
