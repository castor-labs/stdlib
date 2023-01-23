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

namespace Castor\Net\Http;

/**
 * TLSConfig Configuration for the StreamTransport.
 */
class TLSConfig
{
    public function __construct(
        public readonly bool $verify = true,
        public readonly bool $verifyPeerName = true,
        public readonly bool $allowSelfSigned = false,
        public readonly string $caFile = '',
        public readonly string $caPath = '',
        public readonly string $certificate = '',
        public readonly string $privateKey = '',
        public readonly string $passphrase = '',
        public readonly int $verifyDepth = 0,
        public readonly string $cyphers = 'DEFAULT',
    ) {
    }

    public function toContextArray(): array
    {
        $options = [
            'verify_peer' => $this->verify,
            'verify_peer_name' => $this->verifyPeerName,
            'allow_self_signed' => $this->allowSelfSigned,
            'cyphers' => $this->cyphers,
        ];

        if ('' !== $this->caFile) {
            $options['cafile'] = $this->caFile;
        }

        if ('' !== $this->caPath) {
            $options['capath'] = $this->caPath;
        }

        if ('' !== $this->certificate) {
            $options['local_cert'] = $this->certificate;
        }

        if ('' !== $this->privateKey) {
            $options['local_pk'] = $this->privateKey;
        }

        if ('' !== $this->passphrase) {
            $options['passphrase'] = $this->privateKey;
        }

        if ($this->verifyDepth > 0) {
            $options['verify_depth'] = $this->verifyDepth;
        }

        return $options;
    }
}
