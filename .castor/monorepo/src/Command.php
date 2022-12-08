<?php

namespace Castor\Monorepo;

use Composer\Script\Event;
use JsonException;
use RuntimeException;

class Command
{
    /**
     * @param Event $event
     * @return void
     *
     * @throws JsonException
     */
    public static function syncProjects(Event $event): void
    {
        $event->getIO()->write('Syncing workspaces.json to composer autoload...');

        $contents = \file_get_contents('workspaces.json');
        $workspace = json_decode($contents, true);

        $namespace = $workspace['namespace'] ?? '';

        $autoload = $event->getComposer()->getPackage()->getAutoload();
        $autoloadDev = $event->getComposer()->getPackage()->getDevAutoload();

        foreach ($workspace['projects'] ?? [] as $i => $project) {
            $name = $project['name'] ?? '';
            if ($name === '') {
                throw new RuntimeException("Project in position $i does not have name");
            }

            if (!is_dir($name)) {
                $event->getIO()->warning("Directory $name does not exist but project $name is defined in workspaces.json. Skipping...");
                continue;
            }

            $functionFile = $name.DIRECTORY_SEPARATOR.'functions.php';
            $srcDir = $name.DIRECTORY_SEPARATOR.'src';
            $testsDir = $name.DIRECTORY_SEPARATOR.'tests';

            if (is_dir($srcDir)) {
                $paths = $autoload['psr-4'][$namespace] ?? [];
                if (!in_array($srcDir, $paths, true)) {
                    $paths[] = $srcDir;
                    sort($paths);
                }
                $autoload['psr-4'][$namespace] = $paths;
            }

            if (is_file($functionFile)) {
                $files = $autoload['files'] ?? [];
                if (!in_array($functionFile, $files, true)) {
                    $files[] = $functionFile;
                    sort($files);
                }
                $autoload['files'] = $files;
            }

            if (is_dir($testsDir)) {
                $paths = $autoloadDev['psr-4'][$namespace] ?? [];
                if (!in_array($testsDir, $paths, true)) {
                    $paths[] = $testsDir;
                    sort($paths);
                }

                $autoloadDev['psr-4'][$namespace] = $paths;
            }
        }

        $event->getComposer()->getPackage()->setAutoload($autoload);
        $event->getComposer()->getPackage()->setDevAutoload($autoloadDev);

        $event->getComposer()->getConfig()->getConfigSource()->addProperty('autoload', $autoload);
        $event->getComposer()->getConfig()->getConfigSource()->addProperty('autoload-dev', $autoloadDev);
    }
}