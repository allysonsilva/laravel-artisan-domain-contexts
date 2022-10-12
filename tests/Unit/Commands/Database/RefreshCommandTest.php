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
             ->expectsOutput(Mockery::pattern('/Rolling back migrations/'))
             ->expectsOutput(Mockery::pattern('/2019_12_14_000001_create_personal_access_tokens_table/'))
             ->expectsOutput(Mockery::pattern('/2019_08_19_000000_create_failed_jobs_table/'))
             ->expectsOutput(Mockery::pattern('/2014_10_12_100000_create_password_resets_table/'))
             ->expectsOutput(Mockery::pattern('/2014_10_12_000000_create_users_table/'))
            //
             ->expectsOutput(Mockery::pattern('/Running migrations/'))
             ->expectsOutput(Mockery::pattern('/2014_10_12_000000_create_users_table/'))
             ->expectsOutput(Mockery::pattern('/2014_10_12_100000_create_password_resets_table/'))
             ->expectsOutput(Mockery::pattern('/2019_08_19_000000_create_failed_jobs_table/'))
             ->expectsOutput(Mockery::pattern('/2019_12_14_000001_create_personal_access_tokens_table/'))
            //
             ->doesntExpectOutputToContain('2022_01_30_000000_create_users_1_table')
             ->doesntExpectOutputToContain('2022_01_30_000000_create_posts_1_table')
             ->doesntExpectOutputToContain('2022_01_30_000000_create_xyz_1_table')
             ->assertSuccessful();
    }

    /**
     * @test
     * @testdox Using the `--context` option to refresh context-specific migrations
     */
    public function refreshContextMigrations(): void
    {
        $this->artisan('migrate', ['--all-contexts' => true])->assertSuccessful()->run();

        $this->artisan($this->commandName, ['--context' => 'Post'])
             ->expectsOutput(Mockery::pattern('/Rolling back migrations/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_posts_3_table/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_posts_2_table/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_posts_1_table/'))
             //
             ->expectsOutput(Mockery::pattern('/Running migrations/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_posts_1_table/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_posts_2_table/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_posts_3_table/'))
            //
             ->doesntExpectOutputToContain('2019_12_14_000001_create_personal_access_tokens_table')
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
             ->expectsOutput(Mockery::pattern('/Rolling back migrations/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_users_3_table/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_users_2_table/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_users_1_table/'))
             //
             ->expectsOutput(Mockery::pattern('/Running migrations/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_users_1_table/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_users_2_table/'))
             ->expectsOutput(Mockery::pattern('/2022_01_30_000000_create_users_3_table/'))
            //
             ->expectsOutput(Mockery::pattern('/Seeding database/'))
             ->expectsOutput('Run App\Domain\User\Database\Seeders\User1Seeder')
             ->expectsOutput('Run App\Domain\User\Database\Seeders\User2Seeder')
             ->expectsOutput('Run App\Domain\User\Database\Seeders\User3Seeder')
            //
             ->doesntExpectOutputToContain('2019_12_14_000001_create_personal_access_tokens_table')
             ->doesntExpectOutputToContain('Run Database\Seeders\DatabaseSeeder')
             ->doesntExpectOutputToContain('Run App\Domain\Post\Database\Seeders\Post1Seeder')
             ->doesntExpectOutputToContain('Run App\Domain\Foo\Database\Seeders\Xyz1Seeder')
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
             ->expectsOutput(Mockery::pattern('/Rolling back migrations/'))
             ->expectsOutput(Mockery::pattern('/2019_12_14_000001_create_personal_access_tokens_table/'))
             ->expectsOutput(Mockery::pattern('/2019_08_19_000000_create_failed_jobs_table/'))
             ->expectsOutput(Mockery::pattern('/2014_10_12_100000_create_password_resets_table/'))
             ->expectsOutput(Mockery::pattern('/2014_10_12_000000_create_users_table/'))
            //
             ->expectsOutput(Mockery::pattern('/Running migrations/'))
             ->expectsOutput(Mockery::pattern('/2014_10_12_000000_create_users_table/'))
             ->expectsOutput(Mockery::pattern('/2014_10_12_100000_create_password_resets_table/'))
             ->expectsOutput(Mockery::pattern('/2019_08_19_000000_create_failed_jobs_table/'))
             ->expectsOutput(Mockery::pattern('/2019_12_14_000001_create_personal_access_tokens_table/'))
            //
             ->expectsOutput(Mockery::pattern('/Seeding database/'))
             ->expectsOutput('Run Database\Seeders\DatabaseSeeder')
             ->doesntExpectOutputToContain('Run App\Domain\User\Database\Seeders\User1Seeder')
             ->doesntExpectOutputToContain('Run App\Domain\Post\Database\Seeders\Post1Seeder')
             ->doesntExpectOutputToContain('Run App\Domain\Foo\Database\Seeders\Xyz1Seeder')
             ->assertSuccessful();
    }
}
