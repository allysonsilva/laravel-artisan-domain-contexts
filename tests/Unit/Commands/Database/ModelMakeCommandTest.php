<?php

namespace Allyson\ArtisanDomainContext\Tests\Unit\Commands\Database;

use Mockery;
use Illuminate\Support\Facades\File;
use Allyson\ArtisanDomainContext\Tests\Unit\MakeCommandTestCase;

/**
 * @group Making
 * @group Database
 */
class ModelMakeCommandTest extends MakeCommandTestCase
{
    private string $commandName = 'make:model';
    private string $returnMessage = 'Model (?:.+) created successfully';

    /**
     * Class name for use in assertions.
     *
     * @return string
     */
    protected function className(): string
    {
        return "Foo{$this->randomString()}Model";
    }

    /**
     * Component folder name. Where are the component classes.
     *
     * @return string
     */
    protected function componentFolder(): string
    {
        return config('context.folders.components.models');
    }

    /**
     * @test
     * @testdox A "model" must be created in the context folder of the --context option
     */
    public function usingContextOptionWhenCreatingAModel()
    {
        $modelClassName = $this->className();
        $componentFolder = $this->componentFolder();

        $this->artisan($this->commandName, ['--context' => 'User', 'name' => $modelClassName])
             ->assertSuccessful()
             ->expectsOutput(Mockery::pattern("/{$this->returnMessage}/"));

        /** @var \ReflectionClass $modelClass */
        [$modelClass, $file] = $this->getContextComponentClass($modelClassName, $componentFolder, 'User');

        File::deleteDirectory(dirname($file));

        $this->assertSame($modelClass->getShortName(), $modelClassName);
        $this->assertSame($modelClass->getNamespaceName(), $this->getDomainComponentNamespace($componentFolder, 'User'));
    }

    /**
     * @test
     * @testdox When the --context option is not present in the command, then the command should act in the standard Laravel way
     */
    public function savingModelInLaravelDefaultFolder()
    {
        $modelClassName = $this->className();

        $this->artisan($this->commandName, ['name' => $modelClassName])
             ->assertSuccessful()
             ->expectsOutput(Mockery::pattern("/{$this->returnMessage}/"));

        $modelClass = $this->getClass($modelClassName, app_path());

        File::delete(app_path($modelClassName . '.php'));

        $this->assertSame($modelClass->getShortName(), $modelClassName);
        $this->assertSame($modelClass->getNamespaceName(), 'App');
    }

    /**
     * @test
     * @testdox Test using the `--context-namespace` option when creating the models
     */
    public function usingCustomNamespaceOptionWhenCreatingAModel()
    {
        $modelClassName = $this->className();
        $componentFolder = $this->componentFolder();
        $contextNamespace = $this->contextFolder . 'Context';

        $this->artisan($this->commandName, [
                        '--context' => $this->contextFolder,
                        '--context-namespace' => $contextNamespace,
                        'name' => $modelClassName])
             ->assertSuccessful()
             ->expectsOutput(Mockery::pattern("/{$this->returnMessage}/"));

        /** @var \ReflectionClass $modelClass */
        [$modelClass, $file] = $this->getContextComponentClass($modelClassName, $componentFolder);

        $componentCustomNamespace = $this->getDomainComponentCustomNamespace($contextNamespace, $componentFolder);

        File::deleteDirectory(dirname($file));

        $this->assertSame($modelClass->getShortName(), $modelClassName);
        $this->assertSame($modelClass->getNamespaceName(), $componentCustomNamespace);
    }

    /**
     * @test
     * @testdox Using custom options to create additional model files
     */
    public function creatingAdditionalModelFeaturesInContext()
    {
        $modelClassName = $this->className();
        $context = 'Bar';

        $this->artisan($this->commandName, [
                        '--context' => $context,
                        '--factory' => true,
                        '--migration' => true,
                        '--policy' => true,
                        '--seed' => true,
                        'name' => $modelClassName])
             ->assertSuccessful()
             ->expectsOutput(Mockery::pattern("/{$this->returnMessage}/"))
             ->expectsOutput(Mockery::pattern('/Factory(?:.+) created successfully/'))
             ->expectsOutput(Mockery::pattern('/Created migration(.*)/mi'))
             ->expectsOutput(Mockery::pattern('/Seeder(?:.+) created successfully/'))
             ->expectsOutput(Mockery::pattern('/Policy(?:.+) created successfully/'));

        $modelPath = $this->getContextComponentPath($this->componentFolder(), $context);
        $factoryPath = $this->getContextComponentPath(config('context.folders.components.factories'), $context);
        $migrationPath = $this->getContextComponentPath(config('context.folders.components.migrations'), $context);
        $policyPath = $this->getContextComponentPath(config('context.folders.components.policies'), $context);
        $seedPath = $this->getContextComponentPath(config('context.folders.components.seeders'), $context);

        self::assertTrue(! empty(File::files($modelPath)));
        self::assertTrue(! empty(File::files($factoryPath)));
        self::assertTrue(! empty(File::files($migrationPath)));
        self::assertTrue(! empty(File::files($policyPath)));
        self::assertTrue(! empty(File::files($seedPath)));

        File::deleteDirectory(app_path($this->domainFolder . '/' . $context));
    }
}
