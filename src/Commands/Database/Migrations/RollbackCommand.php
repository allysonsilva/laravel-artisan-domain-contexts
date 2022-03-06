<?php

namespace Allyson\ArtisanDomainContext\Commands\Database\Migrations;

use Allyson\ArtisanDomainContext\Commands\Concerns\InteractsWithChoices;
use Illuminate\Database\Console\Migrations\RollbackCommand as LaravelRollbackCommand;
use Allyson\ArtisanDomainContext\Commands\Database\Migrations\Concerns\MigrationPaths;
use Allyson\ArtisanDomainContext\Commands\Database\Migrations\Concerns\MigrationOptions;
use Allyson\ArtisanDomainContext\Commands\Database\Migrations\Concerns\RunInMultiDatabases;

class RollbackCommand extends LaravelRollbackCommand
{
    use MigrationPaths;
    use MigrationOptions;
    use InteractsWithChoices;
    use RunInMultiDatabases {
        RunInMultiDatabases::multiDatabasesHandle as handle;
    }
}
