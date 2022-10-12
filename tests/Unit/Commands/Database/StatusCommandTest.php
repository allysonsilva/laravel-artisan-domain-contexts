<?php

namespace Allyson\ArtisanDomainContext\Tests\Unit\Commands\Database;

use Mockery;
use Allyson\ArtisanDomainContext\Tests\Unit\MigrateCommandTestCase;

/**
 * @group Database
 * @group Migration
 */
class StatusCommandTest extends MigrateCommandTestCase
{
    private string $commandName = 'migrate:status';

    /**
     * @test
     * @testdox Use the `--all-contexts` option to list the migration status of all containers
     */
    public function listMigrationStatusFromAllContexts(): void
    {
        $this->artisan('migrate', ['--all-contexts' => true])->assertSuccessful();

        $this->artisan($this->commandName, ['--all-contexts' => true])
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_posts_1_table (?:.+) \[1\] Ran/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_posts_2_table (?:.+) \[1\] Ran/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_posts_3_table (?:.+) \[1\] Ran/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_users_1_table (?:.+) \[1\] Ran/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_users_2_table (?:.+) \[1\] Ran/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_users_3_table (?:.+) \[1\] Ran/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_xyz_1_table (?:.+) \[1\] Ran/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_xyz_2_table (?:.+) \[1\] Ran/'))
             ->assertSuccessful();
    }

    /**
     * @test
     * @testdox Using the `--context` option to filter the status of migrations from a given context
     */
    public function filteringStatusOnlyFromCertainContext(): void
    {
        $this->artisan('migrate', ['--context' => 'User'])
             ->expectsQuestion('Which class/file would you like to run?', ['ALL'])
             ->assertSuccessful();

        $this->artisan($this->commandName, ['--context' => 'User'])
             ->expectsQuestion('Which class/file would you like to run?', ['ALL'])
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_users_1_table (?:.+) \[1\] Ran/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_users_2_table (?:.+) \[1\] Ran/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_users_3_table (?:.+) \[1\] Ran/'))
             ->assertSuccessful();

        $this->artisan($this->commandName, ['--context' => 'Post'])
             ->expectsQuestion('Which class/file would you like to run?', ['ALL'])
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_posts_1_table (?:.+) Pending/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_posts_2_table (?:.+) Pending/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_posts_3_table (?:.+) Pending/'))
             ->assertSuccessful();

        $fooMigrations = [
            'app/'. $this->domainFolder .'/Foo/Database/Migrations/2022_01_30_000000_create_xyz_1_table.php',
            'app/'. $this->domainFolder .'/Foo/Database/Migrations/2022_01_30_000000_create_xyz_2_table.php',
        ];

        $this->artisan($this->commandName, ['--context' => 'Foo'])
             ->expectsQuestion('Which class/file would you like to run?', $fooMigrations)
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_xyz_1_table (?:.+) Pending/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_xyz_2_table (?:.+) Pending/'))
             ->assertSuccessful();
    }

    /**
     * @test
     * @testdox Using the `--only-default` option to know the status of migrations from the default Laravel folder
     */
    public function getMigrationsStatusFromDefaultLaravelDolder(): void
    {
        $this->artisan('migrate', ['--all-contexts' => true])->assertSuccessful();

        $this->artisan($this->commandName, ['--only-default' => true])
             ->expectsOutput(Mockery::pattern('/2014_10_12_000000_create_users_table (?:.+) Pending/'))
             ->expectsOutput(Mockery::pattern('/2014_10_12_100000_create_password_resets_table (?:.+) Pending/'))
             ->expectsOutput(Mockery::pattern('/2019_08_19_000000_create_failed_jobs_table (?:.+) Pending/'))
             ->expectsOutput(Mockery::pattern('/2019_12_14_000001_create_personal_access_tokens_table (?:.+) Pending/'))
             ->assertSuccessful();

        $this->artisan('migrate', ['--only-default' => true])->assertSuccessful();

        $this->artisan($this->commandName, ['--only-default' => true])
             ->expectsOutput(Mockery::pattern('/2014_10_12_000000_create_users_table (?:.+) \[2\] Ran/'))
             ->expectsOutput(Mockery::pattern('/2014_10_12_100000_create_password_resets_table (?:.+) \[2\] Ran/'))
             ->expectsOutput(Mockery::pattern('/2019_08_19_000000_create_failed_jobs_table (?:.+) \[2\] Ran/'))
             ->expectsOutput(Mockery::pattern('/2019_12_14_000001_create_personal_access_tokens_table (?:.+) \[2\] Ran/'))
             ->assertSuccessful();
    }
}
