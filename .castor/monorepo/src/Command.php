<?php

namespace Castor\Monorepo;

use Composer\Script\Event;
use JsonException;

/**
 * TODO:
 *  - SyncProjects properly
 *  - Generate composer.json
 *      - Check dependencies from each package
 *      - Resolve versions
 *  - Create Sub Splits
 *  - Create Origins
 *  - Put Repositories
 *      - Create on Github
 *      - Description
 *      - Tags
 *  - Create Release
 *      - If tag does not exist, we create it
 */
class Command
{
    private const PHP_VERSION = '>=8.1';

    /**
     * @param Event $event
     * @return void
     *
     * @throws JsonException
     *
     * TODO: Include dependencies that are not castor related
     */
    public static function syncProjects(Event $event): void
    {
        $event->getIO()->write('Syncing project data to main composer.json...');

        $projects = Project::findProjects();

        $autoload = $event->getComposer()->getPackage()->getAutoload();
        $autoloadDev = $event->getComposer()->getPackage()->getDevAutoload();

        foreach ($projects as $project) {
            $name = $project->name;

            $composer = $project->getComposer();
            $pkgName = $composer->getName();
            $pkgPHPVersion = $composer->getPHPVersion();

            if ($pkgName !== 'castor/'.$name) {
                $event->getIO()->warning(sprintf(
                    'Invalid package name in %s/composer.json. Expected %s but got %s.',
                    $project->path,
                    'castor/'.$name,
                    $pkgName
                ));
            }

            if ($pkgPHPVersion !== self::PHP_VERSION) {
                $event->getIO()->warning(sprintf(
                    'Incorrect PHP version in %s. Expected %s but got %s.',
                    $pkgName,
                    self::PHP_VERSION,
                    $pkgPHPVersion
                ));
            }

            $pkgAutoload = $composer->getAutoload();
            $pkgAutoloadDev = $composer->getAutoloadDev();

            foreach ($pkgAutoload['psr-4'] ?? [] as $ns => $path) {
                if (!isset($autoload['psr-4'][$ns])) {
                    $autoload['psr-4'][$ns] = [];
                }

                $pkgPath = $name.DIRECTORY_SEPARATOR.$path;
                if (!is_array($autoload['psr-4'][$ns]) || in_array($pkgPath, $autoload['psr-4'][$ns], true)) {
                    continue;
                }

                $autoload['psr-4'][$ns][] = $pkgPath;
            }

            foreach ($pkgAutoload['files'] ?? [] as $path) {
                if (!isset($autoload['files'])) {
                    $autoload['files'] = [];
                }

                $pkgPath = $name.DIRECTORY_SEPARATOR.$path;
                if (!is_array($autoload['files']) || in_array($pkgPath, $autoload['files'], true)) {
                    continue;
                }

                $autoload['files'][] = $pkgPath;
            }

            foreach ($pkgAutoloadDev['psr-4'] ?? [] as $ns => $path) {
                if (!isset($autoloadDev['psr-4'][$ns])) {
                    $autoloadDev['psr-4'][$ns] = [];
                }

                $pkgPath = $name.DIRECTORY_SEPARATOR.$path;
                if (!is_array($autoloadDev['psr-4'][$ns]) || in_array($pkgPath, $autoloadDev['psr-4'][$ns], true)) {
                    continue;
                }

                $autoloadDev['psr-4'][$ns][] = $pkgPath;
            }

            foreach ($pkgAutoloadDev['files'] ?? [] as $path) {
                if (!isset($autoloadDev['files'])) {
                    $autoloadDev['files'] = [];
                }

                $pkgPath = $name.DIRECTORY_SEPARATOR.$path;
                if (!is_array($autoloadDev['files']) || in_array($pkgPath, $autoloadDev['files'], true)) {
                    continue;
                }

                $autoloadDev['files'][] = $pkgPath;
            }
        }

        if (($autoloadDev['files'] ?? []) === []) {
            unset($autoloadDev['files']);
        }

        $event->getComposer()->getPackage()->setAutoload($autoload);
        $event->getComposer()->getPackage()->setDevAutoload($autoloadDev);

        $event->getComposer()->getConfig()->getConfigSource()->addProperty('autoload', $autoload);
        $event->getComposer()->getConfig()->getConfigSource()->addProperty('autoload-dev', $autoloadDev);
    }
}