<?php

namespace Allyson\ArtisanDomainContext\Commands\Database\Seeds;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Allyson\ArtisanDomainContext\Commands\Foundation\Concerns\BuildClass;
use Illuminate\Database\Console\Seeds\SeederMakeCommand as LaravelSeederMakeCommand;

class SeederMakeCommand extends LaravelSeederMakeCommand
{
    use BuildClass;

    /**
     * Retrieves the name of the folder that is used in the namespace.
     *
     * @return string
     */
    protected function getContextComponentFolderNamespace(): string
    {
        return config('context.folders.components.seeders');
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__ . '/stubs/seeder.stub';
    }

    /**
     * Build the class with the given name.
     *
     * @param string $name
     *
     * @return string
     */
    protected function buildClass($name): string
    {
        $namespace = $this->getCustomContextNamespace();

        if ($namespace === trim($this->rootNamespace(), '\\')) {
            $namespace = 'Database\\Seeders';
        }

        $replace = [
            '{{ seederNamespace }}' => $namespace,
        ];

        $searchByValue = array_keys($replace);
        $substituteValue = array_values($replace);
        $stringToSearchValue = GeneratorCommand::buildClass($name);

        return str_replace($searchByValue, $substituteValue, $stringToSearchValue);
    }

    /**
     * Get the destination class path.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getPath($name): string
    {
        if (! empty($this->contextOption())) {
            return GeneratorCommand::getPath($name);
        }

        $name = (string) Str::of($name)->replaceFirst($this->rootNamespace(), '');

        return $this->laravel->databasePath() . '/seeders/' . str_replace('\\', '/', $name) . '.php';
    }

    /**
     * Parse the class name and format according to the root namespace.
     *
     * @param string $name
     *
     * @return string
     */
    protected function qualifyClass($name): string
    {
        return GeneratorCommand::qualifyClass($name);
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return $this->laravel->getNamespace();
    }
}
