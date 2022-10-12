<?php

namespace Allyson\ArtisanDomainContext\Tests\Unit\Commands\Database;

use Mockery;
use Illuminate\Support\Facades\DB;
use Allyson\ArtisanDomainContext\Tests\Unit\MigrateCommandTestCase;

/**
 * @group Database
 * @group Migration
 */
class RollbackCommandTest extends MigrateCommandTestCase
{
    private string $commandName = 'migrate:rollback';

    /**
     * @test
     * @testdox Using `--context` option to revert all context migrations
     */
    public function rollbackAllContextMigrations(): void
    {
        $this->artisan('migrate', ['--all-contexts' => true])->assertSuccessful();
        $this->artisan('migrate', ['--only-default' => true])->assertSuccessful();

        $this->artisan($this->commandName, ['--context' => 'User', '--step' => 99, '--force' => true])
             ->assertSuccessful();

        self::assertFalse(DB::getSchemaBuilder()->hasTable('users_1'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('users_2'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('users_3'));

        self::assertTrue(DB::getSchemaBuilder()->hasTable('posts_1'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('xyz_1'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('users'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('password_resets'));
    }

    /**
     * @test
     * @testdox Using `--only-default` option to revert Laravel default folder migrations
     */
    public function rollbackLaravelDefaultFolderMigrations(): void
    {
        $this->artisan('migrate', ['--only-default' => true])->assertSuccessful();
        $this->artisan('migrate', ['--all-contexts' => true])->assertSuccessful();

        $this->artisan($this->commandName, ['--only-default' => true, '--step' => 99])
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_xyz_1_table(?:.+) Migration not found/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_users_1_table(?:.+) Migration not found/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_posts_1_table(?:.+) Migration not found/'))
             ->expectsOutput(Mockery::pattern('/2019_12_14_000001_create_personal_access_tokens_table/'))
             ->expectsOutput(Mockery::pattern('/2019_08_19_000000_create_failed_jobs_table/'))
             ->expectsOutput(Mockery::pattern('/2014_10_12_100000_create_password_resets_table/'))
             ->expectsOutput(Mockery::pattern('/2014_10_12_000000_create_users_table/'))
             ->assertSuccessful();

        self::assertFalse(DB::getSchemaBuilder()->hasTable('users'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('password_resets'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('failed_jobs'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('personal_access_tokens'));

        self::assertTrue(DB::getSchemaBuilder()->hasTable('users_1'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('posts_1'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('xyz_1'));
    }

    /**
     * @test
     * @testdox Using the `--all-contexts` option to revert migrations of all contexts
     */
    public function rollbackMigrationsFromAllContexts(): void
    {
        $this->artisan('migrate', ['--all-contexts' => true])->assertSuccessful();
        $this->artisan('migrate', ['--only-default' => true])->assertSuccessful();

        $this->artisan($this->commandName, ['--all-contexts' => true, '--step' => 99])
             ->assertSuccessful();

        self::assertFalse(DB::getSchemaBuilder()->hasTable('users_1'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('posts_1'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('xyz_1'));

        self::assertTrue(DB::getSchemaBuilder()->hasTable('users'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('password_resets'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('failed_jobs'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('personal_access_tokens'));
    }

    /**
     * @test
     * @testdox Reverting only migrations chosen in filtering
     */
    public function rollbackOnlyMigrationsChosenInFiltering(): void
    {
        $this->artisan('migrate', ['--all-contexts' => true])->assertSuccessful();
        $this->artisan('migrate', ['--only-default' => true])->assertSuccessful();

        $migrations = [
            'app/'. $this->domainFolder .'/Post/Database/Migrations/2022_01_30_000000_create_posts_2_table.php',
        ];

        $this->artisan($this->commandName, ['--context' => 'Post', '--step' => 99])
             ->expectsQuestion('Which class/file would you like to run?', $migrations)
             ->expectsOutput(Mockery::pattern('/2014_10_12_000000_create_users_table(?:.+) Migration not found/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_users_3_table(?:.+) Migration not found/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_posts_3_table(?:.+) Migration not found/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_posts_2_table/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_posts_1_table(?:.+) Migration not found/'))
             ->assertSuccessful();

        self::assertFalse(DB::getSchemaBuilder()->hasTable('posts_2'));

        self::assertTrue(DB::getSchemaBuilder()->hasTable('posts_1'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('posts_3'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('users_1'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('xyz_1'));

        self::assertTrue(DB::getSchemaBuilder()->hasTable('users'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('password_resets'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('failed_jobs'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('personal_access_tokens'));
    }
}
