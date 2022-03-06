<?php

namespace Allyson\ArtisanDomainContext\Commands\Database\Migrations;

use Allyson\ArtisanDomainContext\Commands\Database\Migrations\Concerns\MigrationPaths;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand as LaravelMigrateMakeCommand;

class MigrateMakeCommand extends LaravelMigrateMakeCommand
{
    use MigrationPaths;
}
