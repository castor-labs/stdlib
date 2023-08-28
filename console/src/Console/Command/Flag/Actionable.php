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

namespace Castor\Console\Command\Flag;

use Castor\Console\Command\Action;
use Castor\Console\Session;
use Castor\Context;

class Actionable extends Boolean implements Action
{
    public function __construct(
        string $name,
        private readonly Action $action,
        string $short = '',
        string $description = '',
        string $explanation = '',
        bool $value = false
    ) {
        parent::__construct($name, $short, $description, $explanation, $value);
    }

    public function execute(Context $ctx, Session $cli): int
    {
        return $this->action->execute($ctx, $cli);
    }

    public function shouldRun(): bool
    {
        return $this->getValue();
    }
}
