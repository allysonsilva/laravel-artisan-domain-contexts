<?php

namespace Allyson\ArtisanDomainContext\Tests\Unit\Commands\Database;

use Mockery;
use Illuminate\Support\Facades\DB;
use Allyson\ArtisanDomainContext\Tests\Unit\MigrateCommandTestCase;

/**
 * @group Database
 * @group Migration
 */
class MigrateCommandTest extends MigrateCommandTestCase
{
    private string $commandName = 'migrate';

    /**
     * @test
     * @testdox Using `--only-default` option to perform migrations from laravel default folder
     */
    public function runningLaravelDefaultFolderMigrations(): void
    {
        $this->artisan($this->commandName, ['--only-default' => true])
             ->assertSuccessful()
             ->expectsOutput(Mockery::pattern('/Running migrations/'))
             ->expectsOutput(Mockery::pattern('/2014_10_12_000000_create_users_table/mi'))
             ->expectsOutput(Mockery::pattern('/2014_10_12_100000_create_password_resets_table/mi'))
             ->expectsOutput(Mockery::pattern('/2019_08_19_000000_create_failed_jobs_table/mi'))
             ->expectsOutput(Mockery::pattern('/2019_12_14_000001_create_personal_access_tokens_table/mi'));

        self::assertTrue(DB::getSchemaBuilder()->hasTable('users'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('password_resets'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('failed_jobs'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('personal_access_tokens'));

        self::assertFalse(DB::getSchemaBuilder()->hasTable('posts_1'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('users_1'));
    }

    /**
     * @test
     * @testdox Using the `--context` option to run only migrations from a given context
     */
    public function runningMigrationsFromAContext(): void
    {
        $migrations = [
            'app/'. $this->domainFolder .'/User/Database/Migrations/2022_01_30_000000_create_users_1_table.php',
            'app/'. $this->domainFolder .'/User/Database/Migrations/2022_01_30_000000_create_users_3_table.php',
        ];

        $this->artisan($this->commandName, ['--context' => 'User'])
             ->expectsQuestion('Which class/file would you like to run?', $migrations)
             ->expectsOutput(Mockery::pattern('/Running migrations/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_users_1_table/mi'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_users_3_table/mi'))
             ->assertSuccessful();

        self::assertTrue(DB::getSchemaBuilder()->hasTable('users_1'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('users_3'));

        self::assertFalse(DB::getSchemaBuilder()->hasTable('users_2'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('posts_1'));
    }

    /**
     * @test
     * @testdox Using `--path` option to run all migrations from folder
     */
    public function runningMigrationsUsingPathOption(): void
    {
        $this->artisan($this->commandName, ['--path' => 'app/'. $this->domainFolder .'/Foo/Database/Migrations/', '--force' => true])
             ->expectsOutput(Mockery::pattern('/Running migrations/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_xyz_1_table/mi'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_xyz_2_table/mi'))
             ->assertSuccessful();

        self::assertTrue(DB::getSchemaBuilder()->hasTable('xyz_1'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('xyz_2'));

        self::assertFalse(DB::getSchemaBuilder()->hasTable('users_1'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('posts_1'));
    }

    /**
     * @test
     * @testdox Performing context migrations non-interactively using the `--force` option
     */
    public function runningContextMigrationsWithForceOption(): void
    {
        $this->artisan($this->commandName, ['--context' => 'Post', '--force' => true])
             ->expectsOutput(Mockery::pattern('/Running migrations/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_posts_1_table/mi'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_posts_2_table/mi'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_posts_3_table/mi'))
             ->assertSuccessful();

        self::assertTrue(DB::getSchemaBuilder()->hasTable('posts_1'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('posts_2'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('posts_3'));

        self::assertFalse(DB::getSchemaBuilder()->hasTable('xyz_1'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('users_1'));
    }

    /**
     * @test
     * @testdox Running migrations from all contexts
     */
    public function runningMigrationsFromAllContexts(): void
    {
        $this->artisan($this->commandName, ['--all-contexts' => true])
             ->expectsOutput(Mockery::pattern('/Running migrations/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_posts_1_table/mi'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_posts_2_table/mi'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_posts_3_table/mi'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_users_1_table/mi'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_users_2_table/mi'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_users_3_table/mi'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_xyz_1_table/mi'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_xyz_2_table/mi'))
             ->assertSuccessful();

        self::assertTrue(DB::getSchemaBuilder()->hasTable('posts_1'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('posts_2'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('posts_3'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('users_1'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('users_2'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('users_3'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('xyz_1'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('xyz_2'));
    }
}
