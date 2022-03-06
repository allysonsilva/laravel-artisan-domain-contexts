<?php

namespace Allyson\ArtisanDomainContext\Commands\Routing;

use Allyson\ArtisanDomainContext\Commands\Foundation\Concerns\BuildClass;
use Illuminate\Routing\Console\MiddlewareMakeCommand as LaravelMiddlewareMakeCommand;

class MiddlewareMakeCommand extends LaravelMiddlewareMakeCommand
{
    use BuildClass;

    /**
     * Retrieves the name of the folder that is used in the namespace.
     *
     * @return string
     */
    protected function getContextComponentFolderNamespace(): string
    {
        return config('context.folders.components.middlewares');
    }
}
