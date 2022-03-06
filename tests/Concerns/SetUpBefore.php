<?php

namespace Allyson\ArtisanDomainContext\Tests\Concerns;

use Illuminate\Contracts\Console\Kernel as IlluminateConsoleKernel;
use Allyson\ArtisanDomainContext\ArtisanDomainContextServiceProvider;
use Allyson\ArtisanDomainContext\Tests\Laravel\Foundation\ConsoleKernel;

trait SetUpBefore
{
    /**
     * Laravel data folder path.
     *
     * @var string
     */
    protected static string $laravelPath;

    /**
     * Application (business) folder path.
     *
     * @var string
     */
    protected static string $applicationPath;

    /**
     * Resolve application setUp.
     *
     * @return void
     */
    public static function resolveSetUpBefore(): void
    {
        static::$laravelPath = realpath(__DIR__ . '/../Laravel');
        static::$applicationPath = realpath(__DIR__ . '/../LaravelApp');
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array<int, string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            ArtisanDomainContextServiceProvider::class,
        ];
    }

    /**
     * Resolve application Console Kernel implementation.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function resolveApplicationConsoleKernel($app)
    {
        $app->singleton(IlluminateConsoleKernel::class, ConsoleKernel::class);
    }

    /**
     * Get Application's base path.
     *
     * @return string
     */
    public static function applicationBasePath()
    {
        return realpath(__DIR__ . '/../LaravelApp');
    }
}
