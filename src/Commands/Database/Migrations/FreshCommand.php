<?php

namespace Allyson\ArtisanDomainContext\Commands\Database\Migrations;

use Allyson\ArtisanDomainContext\Commands\Concerns\CustomOptionsFilters;
use Illuminate\Database\Console\Migrations\FreshCommand as LaravelFreshCommand;
use Allyson\ArtisanDomainContext\Commands\Database\Migrations\Concerns\MigrationPaths;
use Allyson\ArtisanDomainContext\Commands\Database\Migrations\Concerns\MigrationOptions;

class FreshCommand extends LaravelFreshCommand
{
    use MigrationPaths;
    use MigrationOptions;
    use CustomOptionsFilters;

    /**
     * Call another console command.
     *
     * @param \Symfony\Component\Console\Command\Command|string $command
     * @param array $arguments
     *
     * @return int
     *
     * @phpstan-param array<mixed> $arguments
     */
    public function call($command, array $arguments = []): int
    {
        if ($command === 'migrate') {
            $newArguments = array_filter([
                '--only-default' => $this->option('only-default'),
                '--context' => $this->option('context'),
            ]);

            $arguments = array_merge($arguments, $newArguments);
        }

        return parent::call($command, $arguments);
    }

    /**
     * Run the database seeder command.
     *
     * @param  string  $database
     *
     * @return void
     */
    protected function runSeeder($database): void
    {
        $noEmptyValues = array_filter([
            '--database' => $database,
            '--class' => $this->option('seeder') ?? 'Database\\Seeders\\DatabaseSeeder',
            '--only-default' => $this->option('only-default'),
            '--context' => $this->option('context'),
            '--force' => true,
        ]);

        $this->call('db:seed', $noEmptyValues);
    }

    /**
     * Tells whether `--all-contexts` should be in the command options.
     *
     * @return bool
     */
    private function isWithAllContextsOption(): bool
    {
        return false;
    }

    /**
     * Tells whether `--multi-databases` should be in the command options.
     *
     * @return bool
     */
    private function isWithMultiDatabasesOption(): bool
    {
        return false;
    }
}
