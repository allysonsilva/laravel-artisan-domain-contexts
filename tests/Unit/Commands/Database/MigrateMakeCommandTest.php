<?php

namespace Allyson\ArtisanDomainContext\Tests\Unit\Commands\Database;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Contracts\Console\Kernel;
use Allyson\ArtisanDomainContext\Tests\Unit\MakeCommandTestCase;

/**
 * @group Making
 * @group Database
 */
class MigrateMakeCommandTest extends MakeCommandTestCase
{
    private string $commandName = 'make:migration';

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMockingConsoleOutput();
    }

    /**
     * Migration file name.
     *
     * @return string
     */
    protected function migrationFilename(): string
    {
        return "create_{$this->randomString()}_table";
    }

    /**
     * Component folder name. Where are the component classes.
     *
     * @return string
     */
    protected function componentFolder(): string
    {
        return config('context.folders.components.migrations');
    }

    /**
     * @test
     * @testdox A "migration" must be created in the context folder of the --context option
     */
    public function usingContextOptionWhenCreatingAMigration()
    {
        $exitCode = $this->artisan($this->commandName, ['--context' => $this->contextFolder, 'name' => $migrationFilename = $this->migrationFilename()]);
        $consoleOutput = $this->app[Kernel::class]->output();

        [, $filename] = explode(': ', trim($consoleOutput));

        $componentFilepath = $this->getComponentFilepath($filename, $this->componentFolder());

        $this->assertTrue($exitCode === 0);
        $this->assertTrue(str_starts_with($consoleOutput, 'Created Migration: '));
        $this->assertTrue(File::exists($componentFilepath));

        File::delete($componentFilepath);
    }

    /**
     * @test
     * @testdox When the --context option is not present in the command, then the command should act in the standard Laravel way
     */
    public function savingMigrationInLaravelDefaultFolder()
    {
        $exitCode = $this->artisan($this->commandName, ['name' => $migrationFilename = $this->migrationFilename()]);
        $consoleOutput = $this->app[Kernel::class]->output();

        [, $filename] = explode(': ', trim($consoleOutput));

        $componentFilepath = database_path('migrations' . '/' . $filename . '.php');

        $this->assertTrue($exitCode === 0);
        $this->assertTrue(str_starts_with($consoleOutput, 'Created Migration: '));
        $this->assertTrue(File::exists($componentFilepath));

        File::delete($componentFilepath);
    }
}
