<?php

namespace Allyson\ArtisanDomainContext\Commands\Foundation\Concerns;

trait BuildClass
{
    use CommonMakeOptions;

    /**
     * Retrieves the name of the context component folder that will be used in the namespace manipulation.
     *
     * @return string
     */
    abstract protected function getContextComponentFolderNamespace(): string;

    /**
     * Get the context namespace for the class.
     * Get the full namespace for a given class, without the class name.
     *
     * @return string
     */
    protected function getContextNamespace(): string
    {
        $rootNamespace = trim($this->rootNamespace(), '\\');

        if (! empty($contextOption = $this->contextOption())) {
            return $rootNamespace . '\\' .
                    config('context.folders.domain') . '\\' .
                    $contextOption . '\\' .
                    $this->getComponentFolderNamespace();
        }

        return parent::getDefaultNamespace($rootNamespace);
    }

    /**
     * Returns the custom namespace of the class.
     *
     * @return string
     */
    protected function getCustomContextNamespace(): string
    {
        $contextNamespaceOption = $this->contextNamespaceOption();

        if (! empty($contextNamespaceOption)) {
            return trim($contextNamespaceOption, '\\') . '\\' . $this->getComponentFolderNamespace();
        }

        return $this->getContextNamespace();
    }

    /**
     * Returns only the namespace part of the component.
     *
     * @return string
     */
    protected function getComponentFolderNamespace(): string
    {
        return str_replace('/', '\\', $this->getContextComponentFolderNamespace());
    }

    /**
     * Get the default namespace for the class.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string // phpcs:ignore
    {
        return $this->getContextNamespace();
    }

    /**
     * Get the full namespace for a given class, without the class name.
     *
     * @param string $fullNamespace
     *
     * @return string
     */
    protected function getNamespace($fullNamespace): string
    {
        $contextNamespaceOption = $this->contextNamespaceOption();

        if (! empty($contextNamespaceOption)) {
            return trim($contextNamespaceOption, '\\') . '\\' . $this->getComponentFolderNamespace();
        }

        return parent::getNamespace($fullNamespace);
    }

    /**
     * Checks if the `--context` option exists and retrieves its value if this option is passed in the command.
     *
     * @return string|bool
     */
    protected function contextOption(): string|bool
    {
        $optionName = 'context';

        if ($this->hasOption($optionName) && ! empty($contextOption = $this->option($optionName))) {
            return trim($contextOption);
        }

        return false;
    }

    /**
     * Checks if the `--context-namespace` option exists and
     * retrieves its value if this option is passed in the command.
     *
     * @return string|bool
     */
    protected function contextNamespaceOption(): string|bool
    {
        $optionName = 'context-namespace';

        if ($this->hasOption($optionName) && ! empty($contextNamespaceOption = $this->option($optionName))) {
            return trim($contextNamespaceOption);
        }

        return false;
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
        $partsFullClassName = explode('\\', $name);
        $className = end($partsFullClassName);

        return str_replace(['DummyClass', '{{ class }}', '{{class}}'], $className, $stub);
    }
}
