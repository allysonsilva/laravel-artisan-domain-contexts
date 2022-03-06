<?php

namespace Allyson\ArtisanDomainContext\Commands\Foundation;

use Allyson\ArtisanDomainContext\Commands\Foundation\Concerns\BuildClass;
use Illuminate\Foundation\Console\ModelMakeCommand as LaravelModelMakeCommand;

class ModelMakeCommand extends LaravelModelMakeCommand
{
    use BuildClass;

    /**
     * Call another console command.
     *
     * @param \Symfony\Component\Console\Command\Command|string $command
     * @param array $arguments
     *
     * @return int
     *
     * @phpstan-param array<mixed> $arguments
     */
    public function call($command, array $arguments = []): int
    {
        $contextArguments = array_filter([
            '--context-namespace' => $this->option('context-namespace'),
            '--context' => $this->option('context'),
        ]);

        $arguments = array_merge($arguments, $contextArguments);

        return parent::call($command, $arguments);
    }

    /**
     * Retrieves the name of the folder that is used in the namespace.
     *
     * @return string
     */
    protected function getContextComponentFolderNamespace(): string
    {
        return config('context.folders.components.models');
    }
}
