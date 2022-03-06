<?php

namespace Allyson\ArtisanDomainContext\Commands\Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Allyson\ArtisanDomainContext\Commands\Foundation\Concerns\BuildClass;
use Illuminate\Database\Console\Factories\FactoryMakeCommand as LaravelFactoryMakeCommand;

class FactoryMakeCommand extends LaravelFactoryMakeCommand
{
    use BuildClass;

    /**
     * Retrieves the name of the folder that is used in the namespace.
     *
     * @return string
     */
    protected function getContextComponentFolderNamespace(): string
    {
        return config('context.folders.components.factories');
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
        /** @phpstan-ignore-next-line */
        $factory = class_basename(Str::ucfirst(str_replace('Factory', '', $name)));

        $namespace = $this->getCustomContextNamespace();

        if ($namespace === trim($this->rootNamespace(), '\\')) {
            $namespace = 'Database\\Factories';
        }

        $replace = [
            '{{ factoryNamespace }}' => $namespace,
            '{{ factory }}' => $factory,
            '{{factory}}' => $factory,
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
            /** @phpstan-ignore-next-line */
            $name = (string) Str::of($name)->replaceFirst($this->rootNamespace(), '')->finish('Factory');

            return $this->laravel->path() . '/' . str_replace('\\', '/', $name) . '.php';
        }

        return parent::getPath($name);
    }
}
