<?php

namespace Allyson\ArtisanDomainContext\Commands\Foundation\Concerns;

use Symfony\Component\Console\Input\InputOption;

trait CommonMakeOptions
{
    /**
     * Get the console command options.
     *
     * @return array
     *
     * @phpstan-return InputOptionsArray
     */
    protected function getOptions(): array
    {
        $options = parent::getOptions();

        array_push($options, $this->getContextNamespaceOption());

        return $options;
    }

    /**
     * Retrieves `--context-namespace` option data.
     *
     * @return \Symfony\Component\Console\Input\InputOption
     */
    protected function getContextNamespaceOption(): InputOption
    {
        $description = 'Namespace alias used in context classes';

        return new InputOption(name: 'context-namespace', mode: InputOption::VALUE_OPTIONAL, description: $description);
    }
}
