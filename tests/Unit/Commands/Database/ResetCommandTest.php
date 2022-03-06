<?php

namespace Allyson\ArtisanDomainContext\Tests\Unit\Commands\Database;

use Mockery;
use Illuminate\Support\Facades\DB;
use Allyson\ArtisanDomainContext\Tests\Unit\MigrateCommandTestCase;

/**
 * @group Database
 * @group Migration
 */
class ResetCommandTest extends MigrateCommandTestCase
{
    private string $commandName = 'migrate:reset';

    /**
     * @test
     * @testdox Using the `--all-contexts` option to rollback migrations from all contexts
     */
    public function rollbackAllMigrationsFromAllContexts(): void
    {
        // Run migrations from all contexts
        // By default does not run migrations from Laravel's default folder
        $this->artisan('migrate', ['--all-contexts' => true])->assertSuccessful();

        // Running Laravel default folder migrations
        $this->artisan('migrate', ['--only-default' => true])->assertSuccessful();

        $this->artisan($this->commandName, ['--all-contexts' => true])
             ->expectsOutput('Rolling back: 2022_01_30_000000_create_xyz_2_table')
             ->expectsOutput(Mockery::pattern('#Rolled back:  2022_01_30_000000_create_xyz_2_table(.*)#'))
             ->expectsOutput('Rolling back: 2022_01_30_000000_create_xyz_1_table')
             ->expectsOutput(Mockery::pattern('#Rolled back:  2022_01_30_000000_create_xyz_1_table(.*)#'))
             ->expectsOutput('Rolling back: 2022_01_30_000000_create_users_3_table')
             ->expectsOutput(Mockery::pattern('#Rolled back:  2022_01_30_000000_create_users_3_table(.*)#'))
             ->expectsOutput('Rolling back: 2022_01_30_000000_create_users_2_table')
             ->expectsOutput(Mockery::pattern('#Rolled back:  2022_01_30_000000_create_users_2_table(.*)#'))
             ->expectsOutput('Rolling back: 2022_01_30_000000_create_users_1_table')
             ->expectsOutput(Mockery::pattern('#Rolled back:  2022_01_30_000000_create_users_1_table(.*)#'))
             ->expectsOutput('Rolling back: 2022_01_30_000000_create_posts_3_table')
             ->expectsOutput(Mockery::pattern('#Rolled back:  2022_01_30_000000_create_posts_3_table(.*)#'))
             ->expectsOutput('Rolling back: 2022_01_30_000000_create_posts_2_table')
             ->expectsOutput(Mockery::pattern('#Rolled back:  2022_01_30_000000_create_posts_2_table(.*)#'))
             ->expectsOutput('Rolling back: 2022_01_30_000000_create_posts_1_table')
             ->expectsOutput(Mockery::pattern('#Rolled back:  2022_01_30_000000_create_posts_1_table(.*)#'))
             ->doesntExpectOutput('Rolling back: 2014_10_12_000000_create_users_table')
             ->doesntExpectOutput('Rolling back: 2014_10_12_100000_create_password_resets_table')
             ->doesntExpectOutput('Rolling back: 2019_08_19_000000_create_failed_jobs_table')
             ->doesntExpectOutput('Rolling back: 2019_12_14_000001_create_personal_access_tokens_table')
             ->assertSuccessful();

        self::assertFalse(DB::getSchemaBuilder()->hasTable('posts_1'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('users_1'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('xyz_1'));

        self::assertTrue(DB::getSchemaBuilder()->hasTable('users'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('password_resets'));
    }

    /**
     * @test
     * @testdox Using `--only-default` option to revert only Laravel default folder migrations
     */
    public function rollbackMigrationsFromLaravelsDefaultFolder(): void
    {
        $this->artisan('migrate', ['--all-contexts' => true])->assertSuccessful();
        $this->artisan('migrate', ['--only-default' => true])->assertSuccessful();

        $this->artisan($this->commandName, ['--only-default' => true])
             ->expectsOutput('Rolling back: 2019_12_14_000001_create_personal_access_tokens_table')
             ->expectsOutput(Mockery::pattern('#Rolled back:  2019_12_14_000001_create_personal_access_tokens_table(.*)#'))
             ->expectsOutput('Rolling back: 2019_08_19_000000_create_failed_jobs_table')
             ->expectsOutput(Mockery::pattern('#Rolled back:  2019_08_19_000000_create_failed_jobs_table(.*)#'))
             ->expectsOutput('Rolling back: 2014_10_12_100000_create_password_resets_table')
             ->expectsOutput(Mockery::pattern('#Rolled back:  2014_10_12_100000_create_password_resets_table(.*)#'))
             ->expectsOutput('Rolling back: 2014_10_12_000000_create_users_table')
             ->expectsOutput(Mockery::pattern('#Rolled back:  2014_10_12_000000_create_users_table(.*)#'))
             ->doesntExpectOutput('Rolling back: 2022_01_30_000000_create_users_1_table')
             ->doesntExpectOutput('Rolling back: 2022_01_30_000000_create_posts_1_table')
             ->assertSuccessful();

        self::assertTrue(DB::getSchemaBuilder()->hasTable('posts_1'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('users_1'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('xyz_1'));

        self::assertFalse(DB::getSchemaBuilder()->hasTable('users'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('password_resets'));
    }

    /**
     * @test
     * @testdox Using the `--context` option to revert only context migrations
     */
    public function rollbackMigrationsFromAcontext(): void
    {
        $this->artisan('migrate', ['--all-contexts' => true])->assertSuccessful();
        $this->artisan('migrate', ['--only-default' => true])->assertSuccessful();

        $this->artisan($this->commandName, ['--context' => 'User', '--force' => true])
             ->expectsOutput('Rolling back: 2022_01_30_000000_create_users_3_table')
             ->expectsOutput(Mockery::pattern('#Rolled back:  2022_01_30_000000_create_users_3_table(.*)#'))
             ->expectsOutput('Rolling back: 2022_01_30_000000_create_users_2_table')
             ->expectsOutput(Mockery::pattern('#Rolled back:  2022_01_30_000000_create_users_2_table(.*)#'))
             ->expectsOutput('Rolling back: 2022_01_30_000000_create_users_1_table')
             ->expectsOutput(Mockery::pattern('#Rolled back:  2022_01_30_000000_create_users_1_table(.*)#'))
             ->doesntExpectOutput('Rolling back: 2022_01_30_000000_create_xyz_1_table')
             ->doesntExpectOutput('Rolling back: 2022_01_30_000000_create_posts_1_table')
             ->doesntExpectOutput('Rolling back: 2014_10_12_000000_create_users_table')
             ->assertSuccessful();

        self::assertTrue(DB::getSchemaBuilder()->hasTable('users'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('posts_1'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('xyz_1'));
    }

    /**
     * @test
     * @testdox Reverting only migrations chosen from the list of filtered migrations
     */
    public function rollbackContextFilteredMigrations(): void
    {
        $this->artisan('migrate', ['--all-contexts' => true])->assertSuccessful();
        $this->artisan('migrate', ['--only-default' => true])->assertSuccessful();

        $migrations = [
            'app/'. $this->domainFolder .'/User/Database/Migrations/2022_01_30_000000_create_users_2_table.php',
        ];

        $this->artisan($this->commandName, ['--context' => 'User'])
             ->expectsQuestion('Which class/file would you like to run?', $migrations)
             ->expectsOutput('Rolling back: 2022_01_30_000000_create_users_2_table')
             ->expectsOutput(Mockery::pattern('#Rolled back:  2022_01_30_000000_create_users_2_table(.*)#'))
             ->doesntExpectOutput('Rolling back: 2022_01_30_000000_create_users_1_table')
             ->doesntExpectOutput('Rolling back: 2022_01_30_000000_create_users_3_table')
             ->doesntExpectOutput('Rolling back: 2022_01_30_000000_create_posts_1_table')
             ->doesntExpectOutput('Rolling back: 2014_10_12_000000_create_users_table')
             ->assertSuccessful();

        self::assertFalse(DB::getSchemaBuilder()->hasTable('users_2'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('users_1'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('users_3'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('posts_1'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('xyz_1'));

        $migrations = [
            'app/'. $this->domainFolder .'/Post/Database/Migrations/2022_01_30_000000_create_posts_1_table.php',
            'app/'. $this->domainFolder .'/Post/Database/Migrations/2022_01_30_000000_create_posts_3_table.php',
        ];

        $this->artisan($this->commandName, ['--context' => 'Post'])
             ->expectsQuestion('Which class/file would you like to run?', $migrations)
             ->expectsOutput('Rolling back: 2022_01_30_000000_create_posts_3_table')
             ->expectsOutput(Mockery::pattern('#Rolled back:  2022_01_30_000000_create_posts_3_table(.*)#'))
             ->expectsOutput('Rolling back: 2022_01_30_000000_create_posts_1_table')
             ->expectsOutput(Mockery::pattern('#Rolled back:  2022_01_30_000000_create_posts_1_table(.*)#'))
             ->doesntExpectOutput('Rolling back: 2022_01_30_000000_create_users_1_table')
             ->doesntExpectOutput('Rolling back: 2022_01_30_000000_create_users_3_table')
             ->doesntExpectOutput('Rolling back: 2022_01_30_000000_create_posts_2_table')
             ->doesntExpectOutput('Rolling back: 2014_10_12_000000_create_users_table')
             ->assertSuccessful();

        self::assertFalse(DB::getSchemaBuilder()->hasTable('posts_1'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('posts_3'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('posts_2'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('users_1'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('xyz_1'));
    }
}
