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

namespace Castor\Obj;

/**
 * @return array<string,mixed>
 *
 * @psalm-pure
 */
function vars(object $object): array
{
    return \get_object_vars($object);
}

/**
 * @param class-string|object $objectOrClass
 *
 * @psalm-pure
 */
function methods(object|string $objectOrClass): array
{
    return \get_class_methods($objectOrClass);
}
