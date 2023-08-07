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
 * @covers \Castor\Uuid\Unknown
 */
class UnknownTest extends TestCase
{
    /**
     * @dataProvider getParseData
     */
    public function testParse(string $in, string $type): void
    {
        $uuid = Unknown::parse($in);
        $this->assertInstanceOf($type, $uuid);
    }

    /**
     * @return array[]
     */
    public static function getParseData(): array
    {
        return [
            'nil' => ['00000000-0000-0000-0000-000000000000', Nil::class],
            'v3' => ['a0f6aad0-cdf5-3ddc-a2ac-0bddb3249309', V3::class],
            'v4' => ['ed9f3c66-4cf0-40fd-a52b-f4826c6f6348', V4::class],
            'v5' => ['5fe80e27-269a-5cce-98c3-989ddd181b71', V5::class],
            'unknown' => ['99cf973d-3fe7-7ee4-88bd-a0991a048794', Unknown::class],
        ];
    }

    /**
     * @throws \JsonException
     */
    public function testSerialization(): void
    {
        $unknown = Unknown::parse('99cf973d-3fe7-7ee4-88bd-a0991a048794');

        $serialized = \serialize($unknown);
        $json = \json_encode(['uuid' => $unknown], JSON_THROW_ON_ERROR);

        $this->assertSame('O:19:"Castor\Uuid\Unknown":1:{i:0;s:36:"99cf973d-3fe7-7ee4-88bd-a0991a048794";}', $serialized);
        $this->assertTrue($unknown->equals(\unserialize($serialized)));
        $this->assertSame('{"uuid":"99cf973d-3fe7-7ee4-88bd-a0991a048794"}', $json);
        $this->assertSame('99cf973d-3fe7-7ee4-88bd-a0991a048794', (string) $unknown);
    }
}
