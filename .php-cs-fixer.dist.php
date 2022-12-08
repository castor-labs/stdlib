<?php

$header = <<<EOF
@project The Castor Standard Library
@link https://github.com/castor-labs/stdlib
@package castor/stdlib
@author Matias Navarro-Carter mnavarrocarter@gmail.com
@license MIT
@copyright 2022 CastorLabs Ltd

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

$cacheFile = '.castor/var/cache/php-cs-fixer';
$cacheDir = dirname($cacheFile);

if (!is_dir($cacheDir) && !mkdir($cacheDir, 0755, true) && !is_dir($cacheDir)) {
    throw new RuntimeException("Could not create directory: $cacheDir");
}

return (new PhpCsFixer\Config())
    ->setCacheFile($cacheFile)
    ->setRiskyAllowed(true)
    ->setRules([
        '@PhpCsFixer' => true,
        'declare_strict_types' => true,
        'header_comment' => ['header' => $header, 'comment_type' => 'PHPDoc'],
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__)
            ->exclude('*.dist.php')
    )
;
