<?php

namespace Allyson\ArtisanDomainContext;

use Illuminate\Support\AggregateServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use Allyson\ArtisanDomainContext\Providers\ArtisanServiceProvider;
use Allyson\ArtisanDomainContext\Providers\MigrationServiceProvider;

class ArtisanDomainContextServiceProvider extends AggregateServiceProvider implements DeferrableProvider
{
    /**
     * The provider class names.
     *
     * @var array<class-string<\Illuminate\Support\ServiceProvider>>
     *
     * @phpstan-ignore-next-line
     */
    protected $providers = [
        ArtisanServiceProvider::class,
        MigrationServiceProvider::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        parent::register();

        if (! app()->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__ . '/../config/context.php', 'context');
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/context.php' => config_path('context.php'),
            ], 'context-config');
        }
    }
}
