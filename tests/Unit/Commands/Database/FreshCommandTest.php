<?php

namespace Allyson\ArtisanDomainContext\Tests\Unit\Commands\Database;

use Mockery;
use Illuminate\Support\Facades\DB;
use Allyson\ArtisanDomainContext\Tests\Unit\MigrateCommandTestCase;

/**
 * @group Database
 * @group Migration
 */
class FreshCommandTest extends MigrateCommandTestCase
{
    private string $commandName = 'migrate:fresh';

    /**
     * @test
     * @testdox By default the "migrate:fresh" command will perform the migration of all application contexts
     */
    public function runningFreshFromAllContexts(): void
    {
        $this->artisan($this->commandName)
             ->expectsOutput('Dropped all tables successfully.')
             ->expectsOutput('Migrating: 2022_01_30_000000_create_posts_1_table')
             ->expectsOutput(Mockery::pattern('#Migrated:  2022_01_30_000000_create_posts_1_table(.*)#'))
             ->expectsOutput('Migrating: 2022_01_30_000000_create_posts_2_table')
             ->expectsOutput(Mockery::pattern('#Migrated:  2022_01_30_000000_create_posts_2_table(.*)#'))
             ->expectsOutput('Migrating: 2022_01_30_000000_create_posts_3_table')
             ->expectsOutput(Mockery::pattern('#Migrated:  2022_01_30_000000_create_posts_3_table(.*)#'))
             ->expectsOutput('Migrating: 2022_01_30_000000_create_users_1_table')
             ->expectsOutput(Mockery::pattern('#Migrated:  2022_01_30_000000_create_users_1_table(.*)#'))
             ->expectsOutput('Migrating: 2022_01_30_000000_create_users_2_table')
             ->expectsOutput(Mockery::pattern('#Migrated:  2022_01_30_000000_create_users_2_table(.*)#'))
             ->expectsOutput('Migrating: 2022_01_30_000000_create_users_3_table')
             ->expectsOutput(Mockery::pattern('#Migrated:  2022_01_30_000000_create_users_3_table(.*)#'))
             ->expectsOutput('Migrating: 2022_01_30_000000_create_xyz_1_table')
             ->expectsOutput(Mockery::pattern('#Migrated:  2022_01_30_000000_create_xyz_1_table(.*)#'))
             ->expectsOutput('Migrating: 2022_01_30_000000_create_xyz_2_table')
             ->expectsOutput(Mockery::pattern('#Migrated:  2022_01_30_000000_create_xyz_2_table(.*)#'))
             ->assertSuccessful();

        self::assertTrue(DB::getSchemaBuilder()->hasTable('posts_1'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('posts_2'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('posts_3'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('users_1'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('users_2'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('users_3'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('xyz_1'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('xyz_2'));

        self::assertFalse(DB::getSchemaBuilder()->hasTable('users'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('password_resets'));
    }

    /**
     * @test
     * @testdox Using the `--only-default` option to run only migrations from Laravel's default folder
     */
    public function runningFreshOnlyDefaultLaravelMigrationFolder(): void
    {
        $this->artisan($this->commandName, ['--only-default' => true, '--seed' => true])
             ->assertSuccessful()
             ->expectsOutput('Dropped all tables successfully.')
             ->expectsOutput('Migrating: 2014_10_12_000000_create_users_table')
             ->expectsOutput(Mockery::pattern('#Migrated:  2014_10_12_000000_create_users_table(.*)#'))
             ->expectsOutput('Migrating: 2014_10_12_100000_create_password_resets_table')
             ->expectsOutput(Mockery::pattern('#Migrated:  2014_10_12_100000_create_password_resets_table(.*)#'))
             ->expectsOutput('Migrating: 2019_08_19_000000_create_failed_jobs_table')
             ->expectsOutput(Mockery::pattern('#Migrated:  2019_08_19_000000_create_failed_jobs_table(.*)#'))
             ->expectsOutput('Migrating: 2019_12_14_000001_create_personal_access_tokens_table')
             ->expectsOutput(Mockery::pattern('#Migrated:  2019_12_14_000001_create_personal_access_tokens_table(.*)#'))
             ->expectsOutput('Run Database\Seeders\DatabaseSeeder')
             ->expectsOutput('Database seeding completed successfully.')
             ->doesntExpectOutput('Run App\Domain\User\Database\Seeders\User1Seeder')
             ->doesntExpectOutput('Run App\Domain\Post\Database\Seeders\Post1Seeder')
             ->doesntExpectOutput('Run App\Domain\Foo\Database\Seeders\Xyz1Seeder');

        self::assertTrue(DB::getSchemaBuilder()->hasTable('users'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('password_resets'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('failed_jobs'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('personal_access_tokens'));

        self::assertFalse(DB::getSchemaBuilder()->hasTable('posts_1'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('posts_2'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('posts_3'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('users_1'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('users_2'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('users_3'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('xyz_1'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('xyz_2'));
    }

    /**
     * @test
     * @testdox Using the `--context` option to run only migrations from a certain context
     */
    public function runFreshFromFivenContext(): void
    {
        $this->artisan($this->commandName, ['--context' => 'Foo'])
             ->assertSuccessful()
             ->expectsOutput('Dropped all tables successfully.')
             ->expectsOutput('Migrating: 2022_01_30_000000_create_xyz_1_table')
             ->expectsOutput(Mockery::pattern('#Migrated:  2022_01_30_000000_create_xyz_1_table(.*)#'))
             ->expectsOutput('Migrating: 2022_01_30_000000_create_xyz_2_table')
             ->expectsOutput(Mockery::pattern('#Migrated:  2022_01_30_000000_create_xyz_2_table(.*)#'));

        self::assertTrue(DB::getSchemaBuilder()->hasTable('xyz_1'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('xyz_2'));

        self::assertFalse(DB::getSchemaBuilder()->hasTable('users'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('password_resets'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('failed_jobs'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('personal_access_tokens'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('posts_1'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('posts_2'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('posts_3'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('users_1'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('users_2'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('users_3'));
    }

    /**
     * @test
     * @testdox Using the `--context` option to run only migrations and seeders from a given context
     */
    public function runFreshOnContextMigrationsAndSeeds(): void
    {
        $this->artisan($this->commandName, ['--context' => 'User', '--seed' => true])
             ->assertSuccessful()
             ->expectsOutput('Dropped all tables successfully.')
             ->expectsOutput('Migrating: 2022_01_30_000000_create_users_1_table')
             ->expectsOutput(Mockery::pattern('#Migrated:  2022_01_30_000000_create_users_1_table(.*)#'))
             ->expectsOutput('Migrating: 2022_01_30_000000_create_users_2_table')
             ->expectsOutput(Mockery::pattern('#Migrated:  2022_01_30_000000_create_users_2_table(.*)#'))
             ->expectsOutput('Migrating: 2022_01_30_000000_create_users_3_table')
             ->expectsOutput(Mockery::pattern('#Migrated:  2022_01_30_000000_create_users_3_table(.*)#'))
             ->expectsOutput('Run App\Domain\User\Database\Seeders\User1Seeder')
             ->expectsOutput('Run App\Domain\User\Database\Seeders\User2Seeder')
             ->expectsOutput('Run App\Domain\User\Database\Seeders\User3Seeder')
             ->expectsOutput('Database seeding completed successfully.')
             ->doesntExpectOutput('Run Database\Seeders\DatabaseSeeder')
             ->doesntExpectOutput('Run App\Domain\Post\Database\Seeders\Post1Seeder')
             ->doesntExpectOutput('Run App\Domain\Foo\Database\Seeders\Xyz1Seeder');

        self::assertTrue(DB::getSchemaBuilder()->hasTable('users_1'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('users_2'));
        self::assertTrue(DB::getSchemaBuilder()->hasTable('users_3'));

        self::assertFalse(DB::getSchemaBuilder()->hasTable('users'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('password_resets'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('posts_1'));
        self::assertFalse(DB::getSchemaBuilder()->hasTable('posts_2'));

        $this->assertDatabaseHas('users_1', [
            'name' => 'Name 1_1',
            'email' => 'email1_1@domain.tld',
        ]);

        $this->assertDatabaseHas('users_2', [
            'name' => 'Name 2_1',
            'email' => 'email2_1@domain.tld',
        ]);

        $this->assertDatabaseHas('users_3', [
            'name' => 'Name 3_3',
            'email' => 'email3_3@domain.tld',
        ]);
    }
}
