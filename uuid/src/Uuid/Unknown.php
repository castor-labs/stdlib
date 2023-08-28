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

use Castor\Uuid;

/**
 * Unknown represents a UUID which version is not known in advance.
 *
 * This class is capable of parsing any UUID. You should use this class when you are not interested in the concrete
 * UUID version you are working with.
 *
 * However, if you require a particular UUID version, it's better to use the parse methods of the particular version class.
 */
class Unknown extends Base
{
    /**
     * Parses a UUID.
     *
     * The return type will always implement Uuid, but it could be any of the implementations available in this library.
     *
     * If you want to conditionally act upon the version parsed, you can use the "instanceof" keyword to figure out the
     * version you are working with.
     *
     * Possible return types can be "Nil", "Max", "V3", "V4", "V5" and "Unknown"
     *
     * @throws ParsingError
     */
    public static function parse(string $uuid): Uuid
    {
        return parent::parse($uuid);
    }
}
