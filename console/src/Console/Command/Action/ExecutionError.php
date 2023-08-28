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

namespace Castor\Console\Command\Action;

class ExecutionError extends \RuntimeException
{
    public function __construct(string $message = '', int $code = 1, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
