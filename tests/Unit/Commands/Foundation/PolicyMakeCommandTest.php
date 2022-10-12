<?php

namespace Allyson\ArtisanDomainContext\Tests\Unit\Commands\Foundation;

use Mockery;
use PhpToken;
use Illuminate\Support\Facades\File;
use Allyson\ArtisanDomainContext\Tests\Unit\MakeCommandTestCase;

/**
 * @group Making
 */
class PolicyMakeCommandTest extends MakeCommandTestCase
{
    private string $commandName = 'make:policy';

    /**
     * Class name for use in assertions.
     *
     * @return string
     */
    protected function className(): string
    {
        return "Foo{$this->randomString()}Policy";
    }

    /**
     * Model class name for use in assertions.
     *
     * @return string
     */
    protected function modelClassName(): string
    {
        return 'YourModel';
    }

    /**
     * Component folder name. Where are the component classes.
     *
     * @return string
     */
    protected function componentFolder(): string
    {
        return config('context.folders.components.policies');
    }

    /**
     * @test
     * @testdox When using the `--model` option and also the `--context` options, then the model must be referenced to the context namespace
     */
    public function creatingPolicyWithCustomModel()
    {
        $policyClassName = $this->className();

        $modelsFolder = config('context.folders.components.models');
        $modelsComponentNamespace = $this->getDomainComponentNamespace($modelsFolder, $this->contextFolder) . "\\{$this->modelClassName()}";

        $this->artisan($this->commandName, [
                '--context' => $this->contextFolder,
                '--model' => $this->modelClassName(),
                'name' => $policyClassName
            ])
            ->assertSuccessful()
            ->expectsOutput(Mockery::pattern('/Policy (?:.+) created successfully/'));

        $policyFilepath = $this->getComponentFilepath($policyClassName, $this->componentFolder(), $this->contextFolder);
        $hasModelInUseStatement = $this->hasModelInUseStatement($policyFilepath, $modelsComponentNamespace);

        File::delete($policyFilepath);

        self::assertTrue($hasModelInUseStatement);
    }

    /**
     * @test
     * @testdox Creating a policy in laravel's default folder with a custom model with the `--model` option
     */
    public function creatingPolicyWithCustomModelInLaravelDefaultFolder()
    {
        $policyClassName = $this->className();
        $modelsPath = app_path('Models');

        File::makeDirectory(path: $modelsPath, force: true);

        $this->artisan($this->commandName, [
                '--model' => $this->modelClassName(),
                'name' => $policyClassName
            ])
            ->assertSuccessful()
            ->expectsOutput(Mockery::pattern('/Policy (?:.+) created successfully/'));

        $policyFilepath = app_path("Policies/{$policyClassName}.php");
        $hasModelInUseStatement = $this->hasModelInUseStatement($policyFilepath, "App\\Models\\{$this->modelClassName()}");

        File::delete($policyFilepath);
        File::deleteDirectory($modelsPath);

        self::assertTrue($hasModelInUseStatement);
    }

    /**
     * Checks if the policy class has a use statement referring to the model.
     *
     * @param string $policyFilepath
     * @param string $modelsComponentNamespace
     *
     * @return bool
     */
    private function hasModelInUseStatement(string $policyFilepath, string $modelsComponentNamespace): bool
    {
        $policyTokens = PhpToken::tokenize(file_get_contents($policyFilepath));
        $tokensFullQualified = array_filter($policyTokens, fn (PhpToken $token) => $token->getTokenName() === 'T_NAME_QUALIFIED');
        $containsModelInUseStatement = ! empty(array_filter($tokensFullQualified, fn (PhpToken $token) => $token->text === $modelsComponentNamespace));

        return $containsModelInUseStatement;
    }
}
