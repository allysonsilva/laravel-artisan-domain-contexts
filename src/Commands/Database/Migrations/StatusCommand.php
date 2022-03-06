<?php

namespace Allyson\ArtisanDomainContext\Commands\Database\Migrations;

use Symfony\Component\Console\Input\InputOption;
use Allyson\ArtisanDomainContext\Commands\Concerns\InteractsWithChoices;
use Illuminate\Database\Console\Migrations\StatusCommand as LaravelStatusCommand;
use Allyson\ArtisanDomainContext\Commands\Database\Migrations\Concerns\MigrationPaths;
use Allyson\ArtisanDomainContext\Commands\Database\Migrations\Concerns\MigrationOptions;
use Allyson\ArtisanDomainContext\Commands\Database\Migrations\Concerns\RunInMultiDatabases;

class StatusCommand extends LaravelStatusCommand
{
    use MigrationPaths;
    use MigrationOptions;
    use InteractsWithChoices;
    use RunInMultiDatabases {
        RunInMultiDatabases::multiDatabasesHandle as handle;
    }

    /**
     * Get the console command options.
     *
     * @phpstan-return InputOptionsArray
     */
    protected function getOptions(): array
    {
        $options = $this->getMigrationOptions();

        $forceOption = ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production', null];

        array_push($options, $forceOption);

        return $options;
    }
}
