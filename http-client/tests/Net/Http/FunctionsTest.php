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

namespace Castor\Net\Http;

use Castor\Context;
use Castor\Io;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class FunctionsTest extends TestCase
{
    public function testGet(): void
    {
        $response = get(Context\nil(), 'https://gpg.mnavarro.dev');
        $hash = md5(Io\readAll($response->body));

        $this->assertSame('1fa0fd2d1bc0ac9b9b60a60a86a318ec', $hash);
    }
}
