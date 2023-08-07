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

use Castor\Debug\Logger\Level;

final class LevelLogger implements Logger
{
    private Logger $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function fatal(string $message, mixed ...$params): void
    {
        $this->logger->log($message, Level::FATAL, ...$params);
    }

    public function error(string $message, mixed ...$params): void
    {
        $this->logger->log($message, Level::ERROR, ...$params);
    }

    public function warn(string $message, mixed ...$params): void
    {
        $this->logger->log($message, Level::WARN, ...$params);
    }

    public function info(string $message, mixed ...$params): void
    {
        $this->logger->log($message, Level::INFO, ...$params);
    }

    public function debug(string $message, mixed ...$params): void
    {
        $this->logger->log($message, Level::DEBUG, ...$params);
    }

    public function trace(string $message, mixed ...$params): void
    {
        $this->logger->log($message, Level::TRACE, ...$params);
    }

    public function log(string $message, mixed ...$params): void
    {
        $this->logger->log($message, ...$params);
    }
}
