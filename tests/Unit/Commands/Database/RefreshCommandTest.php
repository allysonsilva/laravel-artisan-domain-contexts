<?php

namespace Allyson\ArtisanDomainContext\Tests\Unit\Commands\Database;

use Mockery;
use Allyson\ArtisanDomainContext\Tests\Unit\MigrateCommandTestCase;

/**
 * @group Database
 * @group Migration
 */
class RefreshCommandTest extends MigrateCommandTestCase
{
    private string $commandName = 'migrate:refresh';

    /**
     * @test
     * @testdox Using `--only-default` option to refresh only Laravel default folder migrations
     */
    public function refreshOnlyMigrationsFromDefaultLaravelFolder(): void
    {
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
            //
             ->expectsOutput('Migrating: 2014_10_12_000000_create_users_table')
             ->expectsOutput(Mockery::pattern('#Migrated:  2014_10_12_000000_create_users_table(.*)#'))
             ->expectsOutput('Migrating: 2014_10_12_100000_create_password_resets_table')
             ->expectsOutput(Mockery::pattern('#Migrated:  2014_10_12_100000_create_password_resets_table(.*)#'))
             ->expectsOutput('Migrating: 2019_08_19_000000_create_failed_jobs_table')
             ->expectsOutput(Mockery::pattern('#Migrated:  2019_08_19_000000_create_failed_jobs_table(.*)#'))
             ->expectsOutput('Migrating: 2019_12_14_000001_create_personal_access_tokens_table')
             ->expectsOutput(Mockery::pattern('#Migrated:  2019_12_14_000001_create_personal_access_tokens_table(.*)#'))
            //
             ->doesntExpectOutput('Migrating: 2022_01_30_000000_create_users_1_table')
             ->doesntExpectOutput('Migrating: 2022_01_30_000000_create_posts_1_table')
             ->doesntExpectOutput('Migrating: 2022_01_30_000000_create_xyz_1_table')
             ->assertSuccessful();
    }

    /**
     * @test
     * @testdox Using the `--context` option to refresh context-specific migrations
     */
    public function refreshContextMigrations(): void
    {
        $this->artisan('migrate', ['--all-contexts' => true])->assertSuccessful();

        $this->artisan($this->commandName, ['--context' => 'Post'])
             ->expectsOutput('Rolling back: 2022_01_30_000000_create_posts_3_table')
             ->expectsOutput(Mockery::pattern('#Rolled back:  2022_01_30_000000_create_posts_3_table(.*)#'))
             ->expectsOutput('Rolling back: 2022_01_30_000000_create_posts_2_table')
             ->expectsOutput(Mockery::pattern('#Rolled back:  2022_01_30_000000_create_posts_2_table(.*)#'))
             ->expectsOutput('Rolling back: 2022_01_30_000000_create_posts_1_table')
             ->expectsOutput(Mockery::pattern('#Rolled back:  2022_01_30_000000_create_posts_1_table(.*)#'))
             //
             ->expectsOutput('Migrating: 2022_01_30_000000_create_posts_1_table')
             ->expectsOutput(Mockery::pattern('#Migrated:  2022_01_30_000000_create_posts_1_table(.*)#'))
             ->expectsOutput('Migrating: 2022_01_30_000000_create_posts_2_table')
             ->expectsOutput(Mockery::pattern('#Migrated:  2022_01_30_000000_create_posts_2_table(.*)#'))
             ->expectsOutput('Migrating: 2022_01_30_000000_create_posts_3_table')
             ->expectsOutput(Mockery::pattern('#Migrated:  2022_01_30_000000_create_posts_3_table(.*)#'))
            //
             ->doesntExpectOutput('Migrating: 2022_01_30_000000_create_users_1_table')
             ->doesntExpectOutput('Migrating: 2022_01_30_000000_create_xyz_1_table')
             ->doesntExpectOutput('Migrating: 2019_12_14_000001_create_personal_access_tokens_table')
             ->assertSuccessful();
    }

    /**
     * @test
     * @testdox Using the `--context` option together with the `--seed` option to run migrations and context seeds
     */
    public function refreshContextMigrationsAlongWithYourSeeds(): void
    {
        $this->artisan('migrate', ['--all-contexts' => true])->assertSuccessful();

        $this->artisan($this->commandName, ['--context' => 'User', '--seed' => true])
             ->expectsOutput('Rolling back: 2022_01_30_000000_create_users_3_table')
             ->expectsOutput(Mockery::pattern('#Rolled back:  2022_01_30_000000_create_users_3_table(.*)#'))
             ->expectsOutput('Rolling back: 2022_01_30_000000_create_users_2_table')
             ->expectsOutput(Mockery::pattern('#Rolled back:  2022_01_30_000000_create_users_2_table(.*)#'))
             ->expectsOutput('Rolling back: 2022_01_30_000000_create_users_1_table')
             ->expectsOutput(Mockery::pattern('#Rolled back:  2022_01_30_000000_create_users_1_table(.*)#'))
             //
             ->expectsOutput('Migrating: 2022_01_30_000000_create_users_1_table')
             ->expectsOutput(Mockery::pattern('#Migrated:  2022_01_30_000000_create_users_1_table(.*)#'))
             ->expectsOutput('Migrating: 2022_01_30_000000_create_users_2_table')
             ->expectsOutput(Mockery::pattern('#Migrated:  2022_01_30_000000_create_users_2_table(.*)#'))
             ->expectsOutput('Migrating: 2022_01_30_000000_create_users_3_table')
             ->expectsOutput(Mockery::pattern('#Migrated:  2022_01_30_000000_create_users_3_table(.*)#'))
            //
             ->expectsOutput('Run App\Domain\User\Database\Seeders\User1Seeder')
             ->expectsOutput('Run App\Domain\User\Database\Seeders\User2Seeder')
             ->expectsOutput('Run App\Domain\User\Database\Seeders\User3Seeder')
             ->expectsOutput('Database seeding completed successfully.')
            //
             ->doesntExpectOutput('Migrating: 2019_12_14_000001_create_personal_access_tokens_table')
             ->doesntExpectOutput('Migrating: 2022_01_30_000000_create_posts_1_table')
             ->doesntExpectOutput('Migrating: 2022_01_30_000000_create_xyz_1_table')
             ->doesntExpectOutput('Run Database\Seeders\DatabaseSeeder')
             ->doesntExpectOutput('Run App\Domain\Post\Database\Seeders\Post1Seeder')
             ->doesntExpectOutput('Run App\Domain\Foo\Database\Seeders\Xyz1Seeder')
             ->assertSuccessful();
    }

    /**
     * @test
     * @testdox Using the `--only-default` option together with the `--seed` option to run the migrations and seeds from the default Laravel folder
     */
    public function refreshMigrationsAlongWithSeedsFromDefaultLaravelFolder(): void
    {
        $this->artisan('migrate', ['--only-default' => true])->assertSuccessful();

        $this->artisan($this->commandName, ['--only-default' => true, '--seed' => true])
             ->expectsOutput('Rolling back: 2019_12_14_000001_create_personal_access_tokens_table')
             ->expectsOutput(Mockery::pattern('#Rolled back:  2019_12_14_000001_create_personal_access_tokens_table(.*)#'))
             ->expectsOutput('Rolling back: 2019_08_19_000000_create_failed_jobs_table')
             ->expectsOutput(Mockery::pattern('#Rolled back:  2019_08_19_000000_create_failed_jobs_table(.*)#'))
             ->expectsOutput('Rolling back: 2014_10_12_100000_create_password_resets_table')
             ->expectsOutput(Mockery::pattern('#Rolled back:  2014_10_12_100000_create_password_resets_table(.*)#'))
             ->expectsOutput('Rolling back: 2014_10_12_000000_create_users_table')
             ->expectsOutput(Mockery::pattern('#Rolled back:  2014_10_12_000000_create_users_table(.*)#'))
            //
             ->expectsOutput('Migrating: 2014_10_12_000000_create_users_table')
             ->expectsOutput(Mockery::pattern('#Migrated:  2014_10_12_000000_create_users_table(.*)#'))
             ->expectsOutput('Migrating: 2014_10_12_100000_create_password_resets_table')
             ->expectsOutput(Mockery::pattern('#Migrated:  2014_10_12_100000_create_password_resets_table(.*)#'))
             ->expectsOutput('Migrating: 2019_08_19_000000_create_failed_jobs_table')
             ->expectsOutput(Mockery::pattern('#Migrated:  2019_08_19_000000_create_failed_jobs_table(.*)#'))
             ->expectsOutput('Migrating: 2019_12_14_000001_create_personal_access_tokens_table')
             ->expectsOutput(Mockery::pattern('#Migrated:  2019_12_14_000001_create_personal_access_tokens_table(.*)#'))
            //
             ->expectsOutput('Run Database\Seeders\DatabaseSeeder')
             ->expectsOutput('Database seeding completed successfully.')
             ->doesntExpectOutput('Run App\Domain\User\Database\Seeders\User1Seeder')
             ->doesntExpectOutput('Run App\Domain\Post\Database\Seeders\Post1Seeder')
             ->doesntExpectOutput('Run App\Domain\Foo\Database\Seeders\Xyz1Seeder')
             ->assertSuccessful();
    }
}
