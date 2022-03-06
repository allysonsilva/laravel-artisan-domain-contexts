<?php

namespace Allyson\ArtisanDomainContext\Concerns;

use Allyson\ArtisanDomainContext\Console\Application as ArtisanApplication;

trait ArtisanConsoleTrait
{
    /**
     * Get the Artisan application instance.
     *
     * @return \Allyson\ArtisanDomainContext\Console\Application
     */
    protected function getArtisan(): ArtisanApplication
    {
        if (is_null($this->artisan)) {
            $consoleApplication = (new ArtisanApplication($this->app, $this->events, $this->app->version()));

            return $this->artisan = $consoleApplication->resolveCommands($this->commands);
        }

        return $this->artisan;
    }
}
