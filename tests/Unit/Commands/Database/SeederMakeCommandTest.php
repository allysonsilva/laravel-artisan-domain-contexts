<?php

namespace Allyson\ArtisanDomainContext\Tests\Unit\Commands\Database;

use Illuminate\Support\Facades\File;
use Allyson\ArtisanDomainContext\Tests\Unit\MakeCommandTestCase;

/**
 * @group Making
 * @group Database
 */
class SeederMakeCommandTest extends MakeCommandTestCase
{
    private string $commandName = 'make:seeder';
    private string $returnMessage = 'Seeder created successfully.';

    /**
     * Class name for use in assertions.
     *
     * @return string
     */
    protected function className(): string
    {
        return "Foo{$this->randomString()}Seeder";
    }

    /**
     * Component folder name. Where are the component classes.
     *
     * @return string
     */
    protected function componentFolder(): string
    {
        return config('context.folders.components.seeders');
    }

    /**
     * @test
     * @testdox A "seeder" must be created in the context folder of the --context option
     */
    public function usingContextOptionWhenCreatingASeeder()
    {
        $seederClassName = $this->className();
        $componentFolder = $this->componentFolder();

        $this->artisan($this->commandName, ['--context' => $this->contextFolder, 'name' => $seederClassName])
             ->assertSuccessful()
             ->expectsOutput($this->returnMessage);

        /** @var \ReflectionClass $seederClass */
        [$seederClass, $file] = $this->getContextComponentClass($seederClassName, $componentFolder);

        File::delete($file);

        $this->assertSame($seederClass->getShortName(), $seederClassName);
        $this->assertSame($seederClass->getNamespaceName(), $this->getDomainComponentNamespace($componentFolder));
    }

    /**
     * @test
     * @testdox When the --context option is not present in the command, then the command should act in the standard Laravel way
     */
    public function savingSeederInLaravelDefaultFolder()
    {
        $seederClassName = $this->className();

        $this->artisan($this->commandName, ['name' => $seederClassName])
             ->assertSuccessful()
             ->expectsOutput($this->returnMessage);

        $seederClass = $this->getClass($seederClassName, database_path('seeders'));

        File::delete(database_path('seeders' . '/' . $seederClassName . '.php'));

        $this->assertSame($seederClass->getShortName(), $seederClassName);
        $this->assertSame($seederClass->getNamespaceName(), 'Database\\Seeders');
    }

    /**
     * @test
     * @testdox Test using the `--context-namespace` option when creating the seeders
     */
    public function usingCustomNamespaceOptionWhenCreatingASeeder()
    {
        $seederClassName = $this->className();
        $componentFolder = $this->componentFolder();
        $contextNamespace = $this->contextFolder . 'Context';

        $this->artisan($this->commandName, [
                        '--context' => $this->contextFolder,
                        '--context-namespace' => $contextNamespace,
                        'name' => $seederClassName])
             ->assertSuccessful()
             ->expectsOutput($this->returnMessage);

        /** @var \ReflectionClass $seederClass */
        [$seederClass, $file] = $this->getContextComponentClass($seederClassName, $componentFolder);

        $componentCustomNamespace = $this->getDomainComponentCustomNamespace($contextNamespace, $componentFolder);

        File::delete($file);

        $this->assertSame($seederClass->getShortName(), $seederClassName);
        $this->assertSame($seederClass->getNamespaceName(), $componentCustomNamespace);
    }
}
