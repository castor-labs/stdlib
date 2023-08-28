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

use Castor\Crypto\Bytes;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \Castor\Uuid\V4
 */
class V4Test extends TestCase
{
    public function testGenerate(): void
    {
        $rand = Bytes::fromUint8(250, 121, 156, 79, 226, 155, 163, 224, 208, 141, 170, 226, 238, 50, 229, 73);
        $uuid = V4::generate($rand);

        $this->assertSame('fa799c4f-e29b-43e0-908d-aae2ee32e549', $uuid->toString());
    }

    /**
     * @dataProvider getParseData
     */
    public function testParse(string $v4): void
    {
        $this->expectNotToPerformAssertions();
        V4::parse($v4);
    }

    /**
     * @dataProvider getParseErrorData
     */
    public function testParseError(string $in, string $message): void
    {
        try {
            V4::parse($in);
        } catch (ParsingError $e) {
            $this->assertSame($message, $e->getMessage());
        }
    }

    public static function getParseErrorData(): array
    {
        return [
            'less chars' => ['00010f-0405-4607-8809-0a0b0c0d0e0f', 'UUID must have 16 bytes.'],
            'more chars' => ['00010203-0405-4607-8809-0a0b0c0d0e0fdf', 'UUID must have 16 bytes.'],
            'wrong version' => ['5bbf8060-4a64-1f50-9e10-882cb74461f7', 'Not a valid version 4 UUID.'],
            'invalid hex' => ['ZZlf8060-4a64-4f50-9e10-882cb74461f7', 'Invalid hexadecimal in UUID.'],
        ];
    }

    public function testGenerateUniqueV4(): void
    {
        $a = V4::generate()->toString();
        $b = V4::generate()->toString();

        $this->assertNotSame($a, $b);
    }

    /**
     * @throws \JsonException
     */
    public function testSerialization(): void
    {
        $v4 = V4::parse('fa06067f-602d-404a-a34c-45c6a7744011');

        $serialized = \serialize($v4);
        $json = \json_encode(['uuid' => $v4], JSON_THROW_ON_ERROR);

        $this->assertSame('O:14:"Castor\Uuid\V4":1:{i:0;s:36:"fa06067f-602d-404a-a34c-45c6a7744011";}', $serialized);
        $this->assertTrue($v4->equals(\unserialize($serialized)));
        $this->assertSame('{"uuid":"fa06067f-602d-404a-a34c-45c6a7744011"}', $json);
        $this->assertSame('fa06067f-602d-404a-a34c-45c6a7744011', (string) $v4);
    }

    public function getParseData(): array
    {
        return [
            ['fa06067f-602d-404a-a34c-45c6a7744011'],
            ['a959e53b-3b3f-4995-a95b-e117ff790662'],
            ['0f342954-20e6-4431-9445-630ad3e8c48a'],
        ];
    }
}
