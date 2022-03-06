<?php

namespace Allyson\ArtisanDomainContext\Contracts;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputDefinition;

interface Inputable
{
    /**
     * @param \Symfony\Component\Console\Input\InputDefinition $definition
     */
    public function __construct(InputDefinition $definition);

    /**
     * Will handle command definition entries.
     *
     * @return void
     */
    public function execute(): void;

    /**
     * Retrieves command option data.
     *
     * @param int $behavior
     *
     * @return \Symfony\Component\Console\Input\InputOption
     */
    public function withOption(int $behavior): InputOption;
}
