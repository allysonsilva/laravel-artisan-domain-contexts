<?php

namespace Allyson\ArtisanDomainContext\Commands\Foundation;

use Allyson\ArtisanDomainContext\Commands\Foundation\Concerns\BuildClass;
use Illuminate\Foundation\Console\ConsoleMakeCommand as LaravelConsoleMakeCommand;

class ConsoleMakeCommand extends LaravelConsoleMakeCommand
{
    use BuildClass {
        replaceClass as replaceDummyClass;
    }

    /**
     * Retrieves the name of the folder that is used in the namespace.
     *
     * @return string
     */
    protected function getContextComponentFolderNamespace(): string
    {
        return config('context.folders.components.console');
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param string $stub
     * @param string $name
     *
     * @return string
     */
    protected function replaceClass($stub, $name): string
    {
        $stub = $this->replaceDummyClass($stub, $name);

        /** @phpstan-ignore-next-line */
        return str_replace(['dummy:command', '{{ command }}'], $this->option('command'), $stub);
    }
}
