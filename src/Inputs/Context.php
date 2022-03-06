<?php

namespace Allyson\ArtisanDomainContext\Inputs;

use Symfony\Component\Console\Input\InputOption;
use Allyson\ArtisanDomainContext\Contracts\Inputable;
use Allyson\ArtisanDomainContext\Inputs\Concerns\ProxyForwardsCalls;

class Context implements Inputable
{
    use ProxyForwardsCalls;

    /**
     * Will add a new entry to the command definition.
     *
     * @return void
     */
    public function execute(): void
    {
        $this->definition->addOption($this->withOption());
    }

    /**
     * Retrieves command option data.
     *
     * @param int $behavior
     *
     * @return \Symfony\Component\Console\Input\InputOption
     */
    public function withOption(int $behavior = InputOption::VALUE_OPTIONAL): InputOption
    {
        $message = 'The context the command should run under';

        return new InputOption('context', null, $behavior, $message);
    }
}
