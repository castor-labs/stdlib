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

namespace Castor\Debug;

use Castor\Context;

final class AppLogger implements Logger
{
    private Logger $logger;
    private string $app;

    public function __construct(Logger $logger, string $app)
    {
        $this->app = $app;
        $this->logger = $logger;
    }

    public function log(Context $ctx, string $message): void
    {
        $this->logger->log(Logger\withApp($ctx, $this->app), $message);
    }
}
