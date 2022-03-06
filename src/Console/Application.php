<?php

namespace Allyson\ArtisanDomainContext\Console;

use Illuminate\Support\Arr;
use Illuminate\Console\Application as Artisan;
use Symfony\Component\Console\Input\InputDefinition;

class Application extends Artisan
{
    /**
     * Get the default input definition for the application.
     *
     * @return \Symfony\Component\Console\Input\InputDefinition
     */
    protected function getDefaultInputDefinition(): InputDefinition
    {
        /** @phpstan-ignore-next-line */
        return tap(parent::getDefaultInputDefinition(), function ($definition) {
            foreach (Arr::wrap(config('context.inputs')) as $inputClass) {
                /** @phpstan-ignore-next-line */
                (new $inputClass($definition))->execute();
            }
        });
    }
}
