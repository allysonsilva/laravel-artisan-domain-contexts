<?php

namespace Allyson\ArtisanDomainContext\Commands\Foundation;

use Allyson\ArtisanDomainContext\Commands\Foundation\Concerns\BuildClass;
use Illuminate\Foundation\Console\PolicyMakeCommand as LaravelPolicyMakeCommand;

class PolicyMakeCommand extends LaravelPolicyMakeCommand
{
    use BuildClass;

    /**
     * Retrieves the name of the folder that is used in the namespace.
     *
     * @return string
     */
    protected function getContextComponentFolderNamespace(): string
    {
        return config('context.folders.components.policies');
    }

    /**
     * Qualify the given model class base name.
     * Get the fully-qualified model class name.
     *
     * @param string $model
     *
     * @return string
     */
    protected function qualifyModel(string $model): string
    {
        $model = ltrim($model, '\\/');
        $model = str_replace('/', '\\', $model);

        $rootNamespace = $this->rootNamespace();

        if (str_starts_with($model, $rootNamespace)) {
            return $model;
        }

        if (! empty($this->contextOption())) {
            $modelsComponentFolder = strval(config('context.folders.components.models'));

            return $this->getContextNamespace($modelsComponentFolder) . "\\{$model}";
        }

        return is_dir(app_path('Models'))
                    ? $rootNamespace . 'Models\\' . $model
                    : $rootNamespace . $model;
    }
}
