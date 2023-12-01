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

use Brick\Math\BigInteger;
use Brick\Math\Exception\MathException;
use Castor\Crypto\Bytes;
use Castor\Encoding\InputError;
use Castor\Uuid\V1\GregorianTime;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \Castor\Uuid\V1
 */
class V1Test extends TestCase
{
    /**
     * @throws InputError
     * @throws MathException
     */
    public function testGenerate(): void
    {
        $state = new V1\Fixed(
            GregorianTime::fromTimestamp(BigInteger::of('139127190012012330')),
            Bytes::fromHex('0001'),
            Bytes::fromHex('00b0d063c226')
        );

        $v1 = V1::generate($state);
        $this->assertSame('3343a72a-4771-11ee-8001-00b0d063c226', $v1->toString());

        $this->assertSame('139127190012012330', (string) $v1->getTime()->getTimestamp());
        $this->assertSame('2023-08-30T20:10:01.201233Z', $v1->getTime()->getInstant()->toISOString());
        $this->assertSame('00b0d063c226', $v1->getNode()->toHex());
        $this->assertSame('0001', $v1->getClockSeq()->toHex());
    }

    /**
     * @throws InputError
     */
    public function testRamseyCompat(): void
    {
        $state = new V1\Fixed(
            new GregorianTime(Bytes::fromHex('01ee4782395c14c4')),
            Bytes::fromHex('17ae'),
            Bytes::fromHex('0242ac1b0004')
        );

        $v1 = V1::generate($state);
        $this->assertSame('395c14c4-4782-11ee-97ae-0242ac1b0004', $v1->toString());
        $this->assertSame('2023-08-30T22:11:52.872058Z', $v1->getTime()->getInstant()->toISOString());
    }

    public function testMultipleGeneration(): void
    {
        $previous = '';
        for ($i = 0; $i < 10000; ++$i) {
            $generated = V1::generate()->toString();
            $this->assertNotSame($generated, $previous);
            $previous = $generated;
        }
    }

    /**
     * @dataProvider getParseErrorData
     */
    public function testParseError(string $in): void
    {
        try {
            V1::parse($in);
        } catch (\Throwable $e) {
            $this->assertInstanceOf(ParsingError::class, $e);
        }
    }

    public static function getParseErrorData(): array
    {
        return [
            'less chars' => ['00010f-0405-4607-8809-0a0b0c0d0e0f'],
            'more chars' => ['00010203-0405-4607-8809-0a0b0c0d0e0fdf'],
            'wrong version' => ['7628f4de-01bd-494b-a84b-d7f900521218'],
            'invalid hex' => ['ZZlf8060-4a64-4f50-9e10-882cb74461f7'],
        ];
    }

    /**
     * @throws \JsonException
     */
    public function testSerialization(): void
    {
        $v1 = V1::parse('5102999c-4771-11ee-be56-0242ac120002');

        $serialized = \serialize($v1);
        $json = \json_encode(['uuid' => $v1], JSON_THROW_ON_ERROR);

        $this->assertSame('O:14:"Castor\Uuid\V1":1:{i:0;s:36:"5102999c-4771-11ee-be56-0242ac120002";}', $serialized);
        $this->assertTrue($v1->equals(\unserialize($serialized)));
        $this->assertSame('{"uuid":"5102999c-4771-11ee-be56-0242ac120002"}', $json);
        $this->assertSame('5102999c-4771-11ee-be56-0242ac120002', (string) $v1);
    }
}
