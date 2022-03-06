<?php

namespace Allyson\ArtisanDomainContext\Tests\Unit\Commands\Database;

use Allyson\ArtisanDomainContext\Exceptions\ClassNotFoundException;
use Allyson\ArtisanDomainContext\Tests\Unit\MigrateCommandTestCase;

/**
 * @group Seeder
 * @group Database
 */
class SeedCommandTest extends MigrateCommandTestCase
{
    private string $commandName = 'db:seed';
    private string $returnMessage = 'Database seeding completed successfully.';

    /**
     * @test
     * @testdox Using the `--only-default` option to run only seeders in Laravel's default folder
     */
    public function runningLaravelDefaultFolderSeeders(): void
    {
        $this->artisan($this->commandName, ['--only-default' => true])
             ->assertSuccessful()
             ->expectsOutput('Run Database\Seeders\DatabaseSeeder')
             ->expectsOutput($this->returnMessage)
             ->doesntExpectOutput('Run App\Domain\User\Database\Seeders\Users1TableSeeder')
             ->doesntExpectOutput('Run App\Domain\Post\Database\Seeders\Post1TableSeeder')
             ->doesntExpectOutput('Run App\Domain\Foo\Database\Seeders\Xyz1TableSeeder');
    }

    /**
     * @test
     * @testdox Using the `--context` option to run only seeders from a given context
     */
    public function runningSeedersFromAContext(): void
    {
        $this->artisan('migrate', ['--context' => 'User', '--force' => true]);

        $seeders = [
            'App\Domain\User\Database\Seeders\User1Seeder',
            'App\Domain\User\Database\Seeders\User3Seeder',
        ];

        $this->artisan($this->commandName, ['--context' => 'User'])
             ->expectsQuestion('Which class/file would you like to run?', $seeders)
             ->expectsOutput('Run App\Domain\User\Database\Seeders\User1Seeder')
             ->expectsOutput('Run App\Domain\User\Database\Seeders\User3Seeder')
             ->assertSuccessful();

        $this->assertDatabaseHas('users_1', [
            'name' => 'Name 1_1',
            'email' => 'email1_1@domain.tld',
        ]);

        $this->assertDatabaseHas('users_3', [
            'name' => 'Name 3_3',
            'email' => 'email3_3@domain.tld',
        ]);
    }

    /**
     * @test
     * @testdox When the chooser class of the seeder does not exist, then an exception should be thrown
     */
    public function throwExceptionWhenSeederClassDoesnotExist(): void
    {
        $this->expectException(ClassNotFoundException::class);
        $this->expectExceptionMessage('NonExistentClass');

        $seeders = [
            'NonExistentClass',
        ];

        $this->artisan($this->commandName)
             ->expectsQuestion('Which class/file would you like to run?', $seeders)
             ->assertSuccessful();
    }

    /**
     * @test
     * @testdox Performing context seeders non-interactively using the `--force` option
     */
    public function runningContextSeedersWithForceOption(): void
    {
        $this->artisan($this->commandName, ['--context' => 'Post', '--force' => true])
             ->expectsOutput('Run App\Domain\Post\Database\Seeders\Post1Seeder')
             ->expectsOutput('Run App\Domain\Post\Database\Seeders\Post2Seeder')
             ->expectsOutput('Run App\Domain\Post\Database\Seeders\Post3Seeder')
             ->assertSuccessful();

        $this->artisan($this->commandName, ['--context' => 'Post'])
             ->expectsQuestion('Which class/file would you like to run?', ['ALL'])
             ->expectsOutput('Run App\Domain\Post\Database\Seeders\Post1Seeder')
             ->expectsOutput('Run App\Domain\Post\Database\Seeders\Post2Seeder')
             ->expectsOutput('Run App\Domain\Post\Database\Seeders\Post3Seeder')
             ->assertSuccessful();
    }

    /**
     * @test
     * @testdox Running seeders from all contexts with choice of options
     */
    public function runningSeedersFromAllContextsWithChoiceOfOptions(): void
    {
        $this->artisan('migrate', ['--all-contexts' => true]);

        $this->artisan($this->commandName)
             ->expectsQuestion('Which class/file would you like to run?', ['ALL'])
             ->expectsOutput('Run App\Domain\Post\Database\Seeders\Post1Seeder')
             ->expectsOutput('Run App\Domain\Post\Database\Seeders\Post2Seeder')
             ->expectsOutput('Run App\Domain\Post\Database\Seeders\Post3Seeder')
             ->expectsOutput('Run App\Domain\User\Database\Seeders\User1Seeder')
             ->expectsOutput('Run App\Domain\User\Database\Seeders\User2Seeder')
             ->expectsOutput('Run App\Domain\User\Database\Seeders\User3Seeder')
             ->expectsOutput('Run App\Domain\Foo\Database\Seeders\Xyz1Seeder')
             ->expectsOutput('Run App\Domain\Foo\Database\Seeders\Xyz2Seeder')
             ->doesntExpectOutput('Run Database\Seeders\DatabaseSeeder')
             ->assertSuccessful();
    }

    /**
     * @test
     * @testdox Running seeders from all contexts with `--all-contexts` option
     */
    public function runningSeedersWithAllContextsOption(): void
    {
        $this->artisan('migrate', ['--all-contexts' => true]);

        $this->artisan($this->commandName, ['--all-contexts' => true])
             ->expectsOutput('Run App\Domain\Post\Database\Seeders\Post1Seeder')
             ->expectsOutput('Run App\Domain\Post\Database\Seeders\Post2Seeder')
             ->expectsOutput('Run App\Domain\Post\Database\Seeders\Post3Seeder')
             ->expectsOutput('Run App\Domain\User\Database\Seeders\User1Seeder')
             ->expectsOutput('Run App\Domain\User\Database\Seeders\User2Seeder')
             ->expectsOutput('Run App\Domain\User\Database\Seeders\User3Seeder')
             ->expectsOutput('Run App\Domain\Foo\Database\Seeders\Xyz1Seeder')
             ->expectsOutput('Run App\Domain\Foo\Database\Seeders\Xyz2Seeder')
             ->doesntExpectOutput('Run Database\Seeders\DatabaseSeeder')
             ->assertSuccessful();
    }
}
