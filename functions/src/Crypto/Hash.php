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

namespace Castor\Crypto;

use Castor\Crypto\Hash\Hasher;
use Castor\Crypto\Hash\NativeHasher;

enum Hash: string
{
    case MD2 = 'md2';

    case MD4 = 'md4';

    case MD5 = 'md5';

    case SHA1 = 'sha1';

    case SHA224 = 'sha224';

    case SHA256 = 'sha256';

    case SHA384 = 'sha384';

    case SHA512_224 = 'sha512/224';

    case SHA512_256 = 'sha512/256';

    case SHA512 = 'sha512';

    public function new(string $contents = ''): Hasher
    {
        return NativeHasher::make($this->value, $contents);
    }
}
