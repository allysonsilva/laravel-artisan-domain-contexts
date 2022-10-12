<?php

namespace Allyson\ArtisanDomainContext\Tests\Unit\Commands\Database;

use Mockery;
use Illuminate\Support\Facades\File;
use Allyson\ArtisanDomainContext\Tests\Unit\MakeCommandTestCase;

/**
 * @group Making
 * @group Database
 */
class FactoryMakeCommandTest extends MakeCommandTestCase
{
    private string $commandName = 'make:factory';
    private string $returnMessage = 'Factory (?:.+) created successfully';

    /**
     * Class name for use in assertions.
     *
     * @return string
     */
    protected function className(): string
    {
        return "Foo{$this->randomString()}Factory";
    }

    /**
     * Component folder name. Where are the component classes.
     *
     * @return string
     */
    protected function componentFolder(): string
    {
        return config('context.folders.components.factories');
    }

    /**
     * @test
     * @testdox A "factory" must be created in the context folder of the --context option
     */
    public function usingContextOptionWhenCreatingAFactory()
    {
        $factoryClassName = $this->className();
        $componentFolder = $this->componentFolder();

        $this->artisan($this->commandName, ['--context' => $this->contextFolder, 'name' => $factoryClassName])
             ->assertSuccessful()
             ->expectsOutput(Mockery::pattern("/{$this->returnMessage}/"));

        /** @var \ReflectionClass $factoryClass */
        [$factoryClass, $file] = $this->getContextComponentClass($factoryClassName, $componentFolder);

        File::delete($file);

        $this->assertSame($factoryClass->getShortName(), $factoryClassName);
        $this->assertSame($factoryClass->getNamespaceName(), $this->getDomainComponentNamespace($componentFolder));
    }

    /**
     * @test
     * @testdox When the --context option is not present in the command, then the command should act in the standard Laravel way
     */
    public function savingFactoryInLaravelDefaultFolder()
    {
        $factoryClassName = $this->className();

        $this->artisan($this->commandName, ['name' => $factoryClassName])
             ->assertSuccessful()
             ->expectsOutput(Mockery::pattern("/{$this->returnMessage}/"));

        $factoryClass = $this->getClass($factoryClassName, database_path('factories'));

        File::delete(database_path('factories' . '/' . $factoryClassName . '.php'));

        $this->assertSame($factoryClass->getShortName(), $factoryClassName);
        $this->assertSame($factoryClass->getNamespaceName(), 'Database\\Factories');
    }

    /**
     * @test
     * @testdox Test using the `--context-namespace` option when creating the factories
     */
    public function usingCustomNamespaceOptionWhenCreatingAFactory()
    {
        $factoryClassName = $this->className();
        $componentFolder = $this->componentFolder();
        $contextNamespace = $this->contextFolder . 'Context';

        $this->artisan($this->commandName, [
                        '--context' => $this->contextFolder,
                        '--context-namespace' => $contextNamespace,
                        'name' => $factoryClassName])
             ->assertSuccessful()
             ->expectsOutput(Mockery::pattern("/{$this->returnMessage}/"));

        /** @var \ReflectionClass $factoryClass */
        [$factoryClass, $file] = $this->getContextComponentClass($factoryClassName, $componentFolder);

        $componentCustomNamespace = $this->getDomainComponentCustomNamespace($contextNamespace, $componentFolder);

        File::delete($file);

        $this->assertSame($factoryClass->getShortName(), $factoryClassName);
        $this->assertSame($factoryClass->getNamespaceName(), $componentCustomNamespace);
    }
}
