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

namespace Castor\Net\Http\Cgi;

use Castor\Io\Closer;
use Castor\Io\Reader;
use Castor\Io\Seeker;

class UploadedFile
{
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly string $filename,
        public readonly int $error,
        public readonly int $size,
    ) {
    }

    /**
     * @param array<string,mixed> $files
     *
     * @return array<string,UploadedFile>
     *
     * TODO: Fix according to https://www.php.net/manual/en/reserved.variables.files.php#89674
     */
    public static function createFromGlobal(array $files = []): array
    {
        $uploadedFiles = [];
        foreach ($files as $key => $file) {
            $uploadedFiles[$key] = new UploadedFile(
                $file['name'],
                $file['type'],
                $file['tmp_name'],
                (int) $file['error'],
                (int) $file['size'],
            );
        }

        return $uploadedFiles;
    }

    public function open(): Reader&Closer&Seeker
    {
        throw new \RuntimeException('Not Implemented');
    }
}
