<?php

namespace Allyson\ArtisanDomainContext\Commands\Foundation;

use Allyson\ArtisanDomainContext\Commands\Foundation\Concerns\BuildClass;
use Illuminate\Foundation\Console\RequestMakeCommand as LaravelRequestMakeCommand;

class RequestMakeCommand extends LaravelRequestMakeCommand
{
    use BuildClass;

    /**
     * Retrieves the name of the folder that is used in the namespace.
     *
     * @return string
     */
    protected function getContextComponentFolderNamespace(): string
    {
        return config('context.folders.components.requests');
    }
}
