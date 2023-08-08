<?php

namespace Castor\Monorepo;

use Generator;
use Symfony\Component\Finder\Finder;

class Project
{
    /**
     * @return Generator<Project>
     */
    public static function findProjects(): Generator
    {
        $cwd = getcwd();
        $finder = Finder::create()->in($cwd)->path('composer.json')->notPath('vendor')->getIterator();

        foreach ($finder as $item) {
            $path = $item->getPath();
            if ($path === $cwd) {
                continue;
            }

            $parts = explode('/', $path);

            yield new Project(
                $path,
                $parts[count($parts) - 1]
            );
        }
    }

    public function __construct(
        public readonly string $path,
        public readonly string $name,
    ) { }

    public function getComposer(): Composer
    {
        return Composer::parseFile($this->path.DIRECTORY_SEPARATOR.'/composer.json');
    }
}