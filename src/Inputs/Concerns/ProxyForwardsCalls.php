<?php

namespace Allyson\ArtisanDomainContext\Inputs\Concerns;

use Illuminate\Support\Traits\ForwardsCalls;
use Symfony\Component\Console\Input\InputDefinition;

trait ProxyForwardsCalls
{
    use ForwardsCalls;

    public function __construct(protected InputDefinition $definition)
    {
    }

    /**
     * Handle dynamic method calls.
     *
     * @param string $name
     * @param array $arguments
     *
     * @return mixed
     *
     * @throws \BadMethodCallException
     *
     * @phpstan-param array<mixed> $arguments
     */
    public function __call(string $name, array $arguments)
    {
        return $this->forwardCallTo($this->definition, $name, $arguments);
    }
}
