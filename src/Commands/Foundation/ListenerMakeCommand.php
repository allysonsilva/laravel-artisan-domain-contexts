<?php

namespace Allyson\ArtisanDomainContext\Commands\Foundation;

use Illuminate\Console\GeneratorCommand;
use Allyson\ArtisanDomainContext\Commands\Foundation\Concerns\BuildClass;
use Illuminate\Foundation\Console\ListenerMakeCommand as LaravelListenerMakeCommand;

class ListenerMakeCommand extends LaravelListenerMakeCommand
{
    use BuildClass;

    /**
     * Retrieves the name of the folder that is used in the namespace.
     *
     * @return string
     */
    protected function getContextComponentFolderNamespace(): string
    {
        return config('context.folders.components.listeners');
    }

    /**
     * Determine if the class already exists.
     *
     * @param string $rawName
     *
     * @return bool
     */
    protected function alreadyExists($rawName): bool
    {
        return GeneratorCommand::alreadyExists($rawName);
    }
}
