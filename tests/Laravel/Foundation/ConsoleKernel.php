<?php

namespace Allyson\ArtisanDomainContext\Tests\Laravel\Foundation;

use Throwable;
use Allyson\ArtisanDomainContext\Concerns\ArtisanConsoleTrait;
use Orchestra\Testbench\Foundation\Console\Kernel as OrchestraConsoleKernel;

class ConsoleKernel extends OrchestraConsoleKernel
{
    use ArtisanConsoleTrait;

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Report the exception to the exception handler.
     *
     * @param  \Throwable  $e
     *
     * @throws \Throwable
     *
     * @return void
     */
    protected function reportException(Throwable $e)
    {
        throw $e;
    }
}
