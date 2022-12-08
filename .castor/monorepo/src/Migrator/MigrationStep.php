<?php

namespace Castor\Monorepo\Migrator;

use Castor\Monorepo\Project;
use Symfony\Component\Finder\Finder;

interface MigrationStep
{
    /**
     * Executes a migration in a project
     *
     * @param Project $project
     * @return void
     */
    public function execute(Project $project): void;
}