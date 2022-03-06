<?php

namespace Allyson\ArtisanDomainContext\Commands\Database\Migrations\Concerns;

use Illuminate\Support\Arr;

trait MigrationPaths
{
    /**
     * Retrieves the name of the folder containing the files.
     *
     * @return string
     */
    protected function getContextComponentFolder(): string
    {
        return config('context.folders.components.migrations');
    }

    /**
     * Get all of the migration paths.
     *
     * @return array<string>
     */
    protected function getMigrationPaths(): array
    {
        /** @phpstan-ignore-next-line */
        if ($this->hasOnlyDefaultOption()) {
            return parent::getMigrationPaths();
        }

        if (false === $this->hasPathOption()) {
            $migrationClasses = $this->getFilesFromContext();

            $chosen = Arr::wrap($this->choose($migrationClasses));

            return $this->addAbsolutePath($chosen);
        }

        return parent::getMigrationPaths();
    }

    /**
     * Get migration path (either specified by '--path' option or default location).
     *
     * @return string
     */
    protected function getMigrationPath(): string
    {
        if (! empty($contextOption = $this->option('context'))) {
            /** @phpstan-ignore-next-line */
            return  $this->laravel->path() . '/' .
                    config('context.folders.domain') . '/' .
                    $contextOption . '/' .
                    $this->getContextComponentFolder();
        }

        return parent::getMigrationPath();
    }

    /**
     * Checks if the `--path` option was passed in the command.
     *
     * @return bool
     */
    protected function hasPathOption(): bool
    {
        $optionName = 'path';

        return $this->hasOption($optionName) && ! empty($this->option($optionName));
    }
}
