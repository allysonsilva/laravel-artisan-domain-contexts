<?php

namespace Allyson\ArtisanDomainContext\Commands\Database\Migrations;

use Symfony\Component\Console\Input\InputOption;
use Allyson\ArtisanDomainContext\Commands\Concerns\InteractsWithChoices;
use Illuminate\Database\Console\Migrations\MigrateCommand as LaravelMigrateCommand;
use Allyson\ArtisanDomainContext\Commands\Database\Migrations\Concerns\MigrationPaths;
use Allyson\ArtisanDomainContext\Commands\Database\Migrations\Concerns\MigrationOptions;
use Allyson\ArtisanDomainContext\Commands\Database\Migrations\Concerns\RunInMultiDatabases;

class MigrateCommand extends LaravelMigrateCommand
{
    use MigrationPaths;
    use MigrationOptions;
    use InteractsWithChoices;
    use RunInMultiDatabases {
        RunInMultiDatabases::multiDatabasesHandle as handle;
    }

    /**
     * Configure the console command using a fluent definition.
     *
     * @return void
     */
    protected function configureUsingFluentDefinition(): void
    {
        parent::configureUsingFluentDefinition();

        foreach ($this->getMigrationOptions() as $options) {
            if ($options instanceof InputOption) {
                $this->getDefinition()->addOption($options); /** @phpstan-ignore-line */
            } else { /** @phpstan-ignore-line */
                /** @phpstan-var InputOptionSignature */
                $optionData = array_values($options);
                $this->addOption(...$optionData);
            }
        }
    }
}
