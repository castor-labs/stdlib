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
use Castor\Debug\Logger\Level;

final class LevelLogger implements Logger
{
    private Logger $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function fatal(Context $ctx, string $message, mixed ...$params): void
    {
        $this->logger->log(Logger\withLevel($ctx, Level::FATAL), $message, ...$params);
    }

    public function error(Context $ctx, string $message, mixed ...$params): void
    {
        $this->logger->log(Logger\withLevel($ctx, Level::ERROR), $message, ...$params);
    }

    public function warn(Context $ctx, string $message, mixed ...$params): void
    {
        $this->logger->log(Logger\withLevel($ctx, Level::WARN), $message, ...$params);
    }

    public function info(Context $ctx, string $message, mixed ...$params): void
    {
        $this->logger->log(Logger\withLevel($ctx, Level::INFO), $message, ...$params);
    }

    public function debug(Context $ctx, string $message, mixed ...$params): void
    {
        $this->logger->log(Logger\withLevel($ctx, Level::DEBUG), $message, ...$params);
    }

    public function trace(Context $ctx, string $message, mixed ...$params): void
    {
        $this->logger->log(Logger\withLevel($ctx, Level::TRACE), $message, ...$params);
    }

    public function log(Context $ctx, string $message, mixed ...$params): void
    {
        $this->logger->log($ctx, $message, ...$params);
    }
}
