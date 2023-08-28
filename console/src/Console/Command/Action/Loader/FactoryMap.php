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

namespace Castor\Console\Command\Action\Loader;

use Castor\Console\Command\Action;
use Castor\Console\Command\Action\Loader;

final class FactoryMap implements Loader
{
    public function load(string $name): Action
    {
        throw new \LogicException('Not Implemented');
    }
}
