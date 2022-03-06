<?php

namespace Allyson\ArtisanDomainContext\Commands\Concerns;

use Symfony\Component\Console\Input\InputOption;

trait CustomOptionsFilters
{
    /**
     * Returns filter options to use in commands.
     *
     * @return array
     *
     * @phpstan-return InputOptionsTypeArray
     */
    protected function withAllFiltersOption(): array
    {
        return [$this->getOnlyDefaultOption(), $this->getAllContextsOption()];
    }

    /**
     * Retrieves `--only-default` option data.
     *
     * @return \Symfony\Component\Console\Input\InputOption
     */
    protected function getOnlyDefaultOption(): InputOption
    {
        $description = "Load component from Laravel's default folder";

        return new InputOption(name: 'only-default', mode: InputOption::VALUE_NONE, description: $description);
    }

    /**
     * Retrieves `--all-contexts` option data.
     *
     * @return \Symfony\Component\Console\Input\InputOption
     */
    protected function getAllContextsOption(): InputOption
    {
        $description = 'Run, filter, show all classes and files selected from all contexts';

        return new InputOption(name: 'all-contexts', mode: InputOption::VALUE_NONE, description: $description);
    }

    /**
     * Checks if the `--only-default` option was passed in the command.
     *
     * @return bool
     */
    protected function hasOnlyDefaultOption(): bool
    {
        $optionName = 'only-default';

        return $this->hasOption($optionName) && boolval($this->option($optionName));
    }

    /**
     * Checks if the `--all-contexts` option was passed in the command.
     *
     * @return bool
     */
    protected function hasAllContextsOption(): bool
    {
        $optionName = 'all-contexts';

        return $this->hasOption($optionName) && boolval($this->option($optionName));
    }
}
