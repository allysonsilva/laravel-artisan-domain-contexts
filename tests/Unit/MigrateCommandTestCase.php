<?php

namespace Allyson\ArtisanDomainContext\Tests\Unit;

use Illuminate\Support\Facades\Artisan;
use Allyson\ArtisanDomainContext\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Contracts\Console\Kernel as IlluminateConsoleKernel;

class MigrateCommandTestCase extends TestCase
{
    use DatabaseTransactions;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->afterApplicationRefreshed(function (): void {
            Artisan::call('db:wipe --force');
            Artisan::call('migrate:install');

            $this->app[IlluminateConsoleKernel::class]->setArtisan(null);
        });

        // $this->beforeApplicationDestroyed(function () {
        //     $this->artisan('migrate:rollback');
        // });

        parent::setUp();
    }
}
