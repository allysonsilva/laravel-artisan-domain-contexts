<?php

namespace Allyson\ArtisanDomainContext\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Support\DeferrableProvider;
use Allyson\ArtisanDomainContext\Commands\Database\Migrations\FreshCommand;
use Allyson\ArtisanDomainContext\Commands\Database\Migrations\ResetCommand;
use Allyson\ArtisanDomainContext\Commands\Database\Migrations\StatusCommand;
use Allyson\ArtisanDomainContext\Commands\Database\Migrations\MigrateCommand;
use Allyson\ArtisanDomainContext\Commands\Database\Migrations\RefreshCommand;
use Allyson\ArtisanDomainContext\Commands\Database\Migrations\RollbackCommand;
use Allyson\ArtisanDomainContext\Commands\Database\Migrations\MigrateMakeCommand;

class MigrationServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * The commands to be registered.
     *
     * @var array<string, string>
     */
    protected $commands = [
        'Migrate' => 'command.migrate',
        'MigrateFresh' => 'command.migrate.fresh',
        'MigrateRefresh' => 'command.migrate.refresh',
        'MigrateReset' => 'command.migrate.reset',
        'MigrateRollback' => 'command.migrate.rollback',
        'MigrateStatus' => 'command.migrate.status',
        'MigrateMake' => 'command.migrate.make',
    ];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerCommands($this->commands);
    }

    /**
     * Get the services provided by the provider.
     *
     * @codeCoverageIgnore
     *
     * @return string[]
     */
    public function provides(): array
    {
        return array_values($this->commands);
    }

    /**
     * Register the given commands.
     *
     * @param array $commands
     *
     * @return void
     *
     * @phpstan-param array<string, string> $commands
     */
    protected function registerCommands(array $commands): void
    {
        foreach ($commands as $command => $abstract) {
            /** @phpstan-ignore-next-line */
            $this->{"register{$command}Command"}($abstract);
        }

        $commands = array_values($commands);

        $this->commands($commands);
    }

    /**
     * Register the command.
     *
     * @param string $abstract
     *
     * @return void
     */
    protected function registerMigrateCommand(string $abstract): void
    {
        $this->app->singleton($abstract, fn ($app) => new MigrateCommand($app['migrator'], $app[Dispatcher::class]));
    }

    /**
     * Register the command.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param string $abstract
     *
     * @return void
     */
    protected function registerMigrateFreshCommand(string $abstract): void
    {
        $this->app->singleton($abstract, fn () => new FreshCommand());
    }

    /**
     * Register the command.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param string $abstract
     *
     * @return void
     */
    protected function registerMigrateRefreshCommand(string $abstract): void
    {
        $this->app->singleton($abstract, fn () => new RefreshCommand());
    }

    /**
     * Register the command.
     *
     * @param string $abstract
     *
     * @return void
     */
    protected function registerMigrateResetCommand(string $abstract): void
    {
        $this->app->singleton($abstract, fn ($app) => new ResetCommand($app['migrator']));
    }

    /**
     * Register the command.
     *
     * @param string $abstract
     *
     * @return void
     */
    protected function registerMigrateRollbackCommand(string $abstract): void
    {
        $this->app->singleton($abstract, fn ($app) => new RollbackCommand($app['migrator']));
    }

    /**
     * Register the command.
     *
     * @param string $abstract
     *
     * @return void
     */
    protected function registerMigrateStatusCommand(string $abstract): void
    {
        $this->app->singleton($abstract, fn ($app) => new StatusCommand($app['migrator']));
    }

    /**
     * Register the command.
     *
     * @param string $abstract
     *
     * @return void
     */
    protected function registerMigrateMakeCommand(string $abstract): void
    {
        $closure = fn ($app) => new MigrateMakeCommand($app['migration.creator'], $app['composer']);

        $this->app->singleton($abstract, $closure);
    }
}
