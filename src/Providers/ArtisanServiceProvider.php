<?php

namespace Allyson\ArtisanDomainContext\Providers;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Contracts\Support\DeferrableProvider;
use Allyson\ArtisanDomainContext\Commands\Foundation\JobMakeCommand;
use Allyson\ArtisanDomainContext\Commands\Database\Seeds\SeedCommand;
use Allyson\ArtisanDomainContext\Commands\Foundation\CastMakeCommand;
use Allyson\ArtisanDomainContext\Commands\Foundation\MailMakeCommand;
use Allyson\ArtisanDomainContext\Commands\Foundation\RuleMakeCommand;
use Allyson\ArtisanDomainContext\Commands\Foundation\EventMakeCommand;
use Allyson\ArtisanDomainContext\Commands\Foundation\ModelMakeCommand;
use Allyson\ArtisanDomainContext\Commands\Foundation\PolicyMakeCommand;
use Allyson\ArtisanDomainContext\Commands\Foundation\ChannelMakeCommand;
use Allyson\ArtisanDomainContext\Commands\Foundation\ConsoleMakeCommand;
use Allyson\ArtisanDomainContext\Commands\Foundation\RequestMakeCommand;
use Allyson\ArtisanDomainContext\Commands\Routing\MiddlewareMakeCommand;
use Allyson\ArtisanDomainContext\Commands\Foundation\ListenerMakeCommand;
use Allyson\ArtisanDomainContext\Commands\Foundation\ObserverMakeCommand;
use Allyson\ArtisanDomainContext\Commands\Foundation\ProviderMakeCommand;
use Allyson\ArtisanDomainContext\Commands\Foundation\ResourceMakeCommand;
use Allyson\ArtisanDomainContext\Commands\Foundation\ExceptionMakeCommand;
use Allyson\ArtisanDomainContext\Commands\Database\Seeds\SeederMakeCommand;
use Allyson\ArtisanDomainContext\Commands\Foundation\NotificationMakeCommand;
use Allyson\ArtisanDomainContext\Commands\Database\Factories\FactoryMakeCommand;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ArtisanServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * The commands to be registered.
     *
     * @var array<string, string>
     */
    protected $commands = [
        'Seed' => 'command.seed',
        'CastMake' => 'command.cast.make',
        'ChannelMake' => 'command.channel.make',
        'ConsoleMake' => 'command.console.make',
        'EventMake' => 'command.event.make',
        'ExceptionMake' => 'command.exception.make',
        'FactoryMake' => 'command.factory.make',
        'JobMake' => 'command.job.make',
        'ListenerMake' => 'command.listener.make',
        'MailMake' => 'command.mail.make',
        'MiddlewareMake' => 'command.middleware.make',
        'ModelMake' => 'command.model.make',
        'NotificationMake' => 'command.notification.make',
        'ObserverMake' => 'command.observer.make',
        'PolicyMake' => 'command.policy.make',
        'ProviderMake' => 'command.provider.make',
        'RequestMake' => 'command.request.make',
        'ResourceMake' => 'command.resource.make',
        'RuleMake' => 'command.rule.make',
        'SeederMake' => 'command.seeder.make',
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

        // if ($this->app->runningInConsole()) {
        //     $closure = function (\Illuminate\Foundation\Console\ModelMakeCommand $command) {
        //         return new App\ModelMakeCommand($this->app['files']);
        //     };

        //     $this->app->extend('command.model.make', $closure);
        // }
    }

    /**
     * Register the command.
     *
     * @param string $abstract
     *
     * @return void
     */
    protected function registerSeedCommand(string $abstract): void
    {
        $this->app->singleton($abstract, fn ($app) => new SeedCommand($app['db']));

        $this->app->extend($abstract, function (SeedCommand $command) {
            return $command->addOption('size', 's', InputOption::VALUE_REQUIRED, 'The size of the seeding', 10);
        });
    }

    /**
     * Register the command.
     *
     * @param string $abstract
     *
     * @return void
     */
    protected function registerCastMakeCommand(string $abstract): void
    {
        $this->app->singleton($abstract, fn ($app) => new CastMakeCommand($app['files']));
    }

    /**
     * Register the command.
     *
     * @param string $abstract
     *
     * @return void
     */
    protected function registerChannelMakeCommand(string $abstract): void
    {
        $this->app->singleton($abstract, fn ($app) => new ChannelMakeCommand($app['files']));
    }

    /**
     * Register the command.
     *
     * @param string $abstract
     *
     * @return void
     */
    protected function registerConsoleMakeCommand(string $abstract): void
    {
        $this->app->singleton($abstract, fn ($app) => new ConsoleMakeCommand($app['files']));
    }

    /**
     * Register the command.
     *
     * @param string $abstract
     *
     * @return void
     */
    protected function registerEventMakeCommand(string $abstract): void
    {
        $this->app->singleton($abstract, fn ($app) => new EventMakeCommand($app['files']));
    }

    /**
     * Register the command.
     *
     * @param string $abstract
     *
     * @return void
     */
    protected function registerExceptionMakeCommand(string $abstract): void
    {
        $this->app->singleton($abstract, fn ($app) => new ExceptionMakeCommand($app['files']));
    }

    /**
     * Register the command.
     *
     * @param string $abstract
     *
     * @return void
     */
    protected function registerFactoryMakeCommand(string $abstract): void
    {
        $this->app->singleton($abstract, fn ($app) => new FactoryMakeCommand($app['files']));
    }

    /**
     * Register the command.
     *
     * @param string $abstract
     *
     * @return void
     */
    protected function registerJobMakeCommand(string $abstract): void
    {
        $this->app->singleton($abstract, fn ($app) => new JobMakeCommand($app['files']));
    }

    /**
     * Register the command.
     *
     * @param string $abstract
     *
     * @return void
     */
    protected function registerListenerMakeCommand(string $abstract): void
    {
        $this->app->singleton($abstract, fn ($app) => new ListenerMakeCommand($app['files']));
    }

    /**
     * Register the command.
     *
     * @param string $abstract
     *
     * @return void
     */
    protected function registerMailMakeCommand(string $abstract): void
    {
        $this->app->singleton($abstract, fn ($app) => new MailMakeCommand($app['files']));
    }

    /**
     * Register the command.
     *
     * @param string $abstract
     *
     * @return void
     */
    protected function registerMiddlewareMakeCommand(string $abstract): void
    {
        $this->app->singleton($abstract, fn ($app) => new MiddlewareMakeCommand($app['files']));
    }

    /**
     * Register the command.
     *
     * @param string $abstract
     *
     * @return void
     */
    protected function registerModelMakeCommand(string $abstract): void
    {
        $this->app->singleton($abstract, fn ($app) => new ModelMakeCommand($app['files']));
    }

    /**
     * Register the command.
     *
     * @param string $abstract
     *
     * @return void
     */
    protected function registerNotificationMakeCommand(string $abstract): void
    {
        $this->app->singleton($abstract, fn ($app) => new NotificationMakeCommand($app['files']));
    }

    /**
     * Register the command.
     *
     * @param string $abstract
     *
     * @return void
     */
    protected function registerObserverMakeCommand(string $abstract): void
    {
        $this->app->singleton($abstract, fn ($app) => new ObserverMakeCommand($app['files']));
    }

    /**
     * Register the command.
     *
     * @param string $abstract
     *
     * @return void
     */
    protected function registerPolicyMakeCommand(string $abstract): void
    {
        $this->app->singleton($abstract, fn ($app) => new PolicyMakeCommand($app['files']));
    }

    /**
     * Register the command.
     *
     * @param string $abstract
     *
     * @return void
     */
    protected function registerProviderMakeCommand(string $abstract): void
    {
        $this->app->singleton($abstract, fn ($app) => new ProviderMakeCommand($app['files']));
    }

    /**
     * Register the command.
     *
     * @param string $abstract
     *
     * @return void
     */
    protected function registerRequestMakeCommand(string $abstract): void
    {
        $this->app->singleton($abstract, fn ($app) => new RequestMakeCommand($app['files']));
    }

    /**
     * Register the command.
     *
     * @param string $abstract
     *
     * @return void
     */
    protected function registerResourceMakeCommand(string $abstract): void
    {
        $this->app->singleton($abstract, fn ($app) => new ResourceMakeCommand($app['files']));
    }

    /**
     * Register the command.
     *
     * @param string $abstract
     *
     * @return void
     */
    protected function registerRuleMakeCommand(string $abstract): void
    {
        $this->app->singleton($abstract, fn ($app) => new RuleMakeCommand($app['files']));
    }

    /**
     * Register the command.
     *
     * @param string $abstract
     *
     * @return void
     */
    protected function registerSeederMakeCommand(string $abstract): void
    {
        $this->app->singleton($abstract, fn ($app) => new SeederMakeCommand($app['files']));
    }
}
