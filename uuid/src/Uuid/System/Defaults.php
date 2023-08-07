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

namespace Castor\Uuid\System;

use Castor\Crypto\Bytes;
use Castor\Encoding\InputError;

final class Defaults implements Node, Clock
{
    /**
     * Pattern to match nodes in ifconfig and ipconfig output.
     */
    private const IFCONFIG_PATTERN = '/[^:]([0-9a-f]{2}([:-])[0-9a-f]{2}(\2[0-9a-f]{2}){4})[^:]/i';

    /**
     * Pattern to match nodes in sysfs stream output.
     */
    private const SYSFS_PATTERN = '/^([0-9a-f]{2}:){5}[0-9a-f]{2}$/i';

    private static ?Defaults $global = null;

    public static function global(): Defaults
    {
        if (null === self::$global) {
            self::$global = new self();
        }

        return self::$global;
    }

    public function read(int $n): string
    {
        try {
            return \random_bytes($n);
        } catch (\Exception $e) {
            throw new \RuntimeException('Not enough entropy', previous: $e);
        }
    }

    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }

    public function getNode(): Bytes
    {
        $node = $this->getNodeFromSystem();

        if ('' === $node) {
            throw new \RuntimeException('Could not determine system node');
        }

        try {
            return Bytes::fromHex($node);
        } catch (InputError $e) {
            throw new \RuntimeException('Invalid system node hexadecimal', previous: $e);
        }
    }

    /**
     * Returns the system node, if it can find it.
     */
    private function getNodeFromSystem(): string
    {
        static $node = null;

        if (null !== $node) {
            return (string) $node;
        }

        // First, try a Linux-specific approach.
        $node = $this->getSysfs();

        if ('' === $node) {
            // Search ifconfig output for MAC addresses & return the first one.
            $node = $this->getIfconfig();
        }

        $node = \str_replace([':', '-'], '', $node);

        return $node;
    }

    /**
     * Returns the network interface configuration for the system.
     *
     * @codeCoverageIgnore
     */
    private function getIfconfig(): string
    {
        $disabledFunctions = \strtolower((string) \ini_get('disable_functions'));

        if (\str_contains($disabledFunctions, 'passthru')) {
            return '';
        }

        \ob_start();

        switch (\strtoupper(\substr(PHP_OS, 0, 3))) {
            case 'WIN':
                \passthru('ipconfig /all 2>&1');

                break;

            case 'DAR':
                \passthru('ifconfig 2>&1');

                break;

            case 'FRE':
                \passthru('netstat -i -f link 2>&1');

                break;

            case 'LIN':
            default:
                \passthru('netstat -ie 2>&1');

                break;
        }

        $ifconfig = (string) \ob_get_clean();

        if (\preg_match_all(self::IFCONFIG_PATTERN, $ifconfig, $matches, PREG_PATTERN_ORDER)) {
            foreach ($matches[1] as $iface) {
                if ('00:00:00:00:00:00' !== $iface && '00-00-00-00-00-00' !== $iface) {
                    return $iface;
                }
            }
        }

        return '';
    }

    /**
     * Returns MAC address from the first system interface via the sysfs interface.
     */
    private function getSysfs(): string
    {
        $mac = '';

        if ('LINUX' === \strtoupper(PHP_OS)) {
            $addressPaths = \glob('/sys/class/net/*/address', GLOB_NOSORT);

            if (false === $addressPaths || 0 === \count($addressPaths)) {
                return '';
            }

            /** @var array<array-key, string> $macs */
            $macs = [];

            \array_walk($addressPaths, static function (string $addressPath) use (&$macs): void {
                if (\is_readable($addressPath)) {
                    $macs[] = \file_get_contents($addressPath);
                }
            });

            $macs = \array_map(trim(...), $macs);

            // Remove invalid entries.
            $macs = \array_filter($macs, static function (string $address) {
                return '00:00:00:00:00:00' !== $address
                    && \preg_match(self::SYSFS_PATTERN, $address);
            });

            /** @var bool|string $mac */
            $mac = \reset($macs);
        }

        return (string) $mac;
    }
}
