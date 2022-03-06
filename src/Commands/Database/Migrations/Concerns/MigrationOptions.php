<?php

namespace Allyson\ArtisanDomainContext\Commands\Database\Migrations\Concerns;

use Symfony\Component\Console\Input\InputOption;

trait MigrationOptions
{
    /**
     * Get the console command options.
     *
     * @phpstan-return InputOptionsArray
     */
    protected function getMigrationOptions(): array
    {
        $options = parent::getOptions();

        if ($this->isWithAllContextsOption()) {
            array_push($options, $this->getAllContextsOption());
        }

        if ($this->isWithMultiDatabasesOption()) {
            array_push($options, $this->getMultiDatabasesOption());
        }

        array_push($options, $this->getOnlyDefaultOption());

        return $options;
    }

    /**
     * Get the console command options.
     *
     * @return array
     *
     * @phpstan-return InputOptionsArray
     */
    protected function getOptions(): array
    {
        return $this->getMigrationOptions();
    }

    /**
     * Retrieves `--multi-databases` option data.
     *
     * @return array
     *
     * @phpstan-return InputOptionSignature
     */
    protected function getMultiDatabasesOption(): array
    {
        return ['multi-databases', null, InputOption::VALUE_NONE, 'Run migrations in all available databases', null];
    }

    /**
     * Tells whether `--all-contexts` should be in the command options.
     *
     * @return bool
     */
    private function isWithAllContextsOption(): bool
    {
        return true;
    }

    /**
     * Tells whether `--multi-databases` should be in the command options.
     *
     * @return bool
     */
    private function isWithMultiDatabasesOption(): bool
    {
        return true;
    }
}
