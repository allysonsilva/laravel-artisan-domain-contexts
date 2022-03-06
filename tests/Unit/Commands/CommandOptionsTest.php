<?php

namespace Allyson\ArtisanDomainContext\Tests\Unit\Commands;

use Allyson\ArtisanDomainContext\Tests\TestCase;
use Allyson\ArtisanDomainContext\Console\Application as ArtisanApplication;

/**
 * @group Options
 */
class CommandOptionsTest extends TestCase
{
    /**
     * @test
     * @testdox Check the options that are available in all Laravel commands
     */
    public function optionsAvailableAllCommands(): void
    {
        /** @var \Allyson\ArtisanDomainContext\Console\Application */
        $consoleApplication = (new ArtisanApplication($this->app, $this->app->make('events'), $this->app->version()));

        /** @var \Symfony\Component\Console\Input\InputDefinition */
        $input = $consoleApplication->getDefinition();

        self::assertTrue($input->hasOption('context'));
    }

    /**
     * @test
     * @testdox Check available options for the `migrate:fresh` command
     */
    public function checkMigrateFreshCommandOptions(): void
    {
        /** @var \Illuminate\Database\Console\Migrations\BaseCommand */
        $command = app('command.migrate.fresh');

        /** @var \Symfony\Component\Console\Input\InputDefinition */
        $definition = $command->getDefinition();

        self::assertTrue($definition->hasOption('only-default'));
        self::assertTrue($definition->hasOption('force'));

        self::assertFalse($definition->hasOption('all-contexts'));
        self::assertFalse($definition->hasOption('context-namespace'));
        self::assertFalse($definition->hasOption('multi-databases'));
    }

    /**
     * @test
     * @testdox Check available options for the `migrate:refresh` command
     */
    public function checkMigrateRefreshCommandOptions(): void
    {
        /** @var \Illuminate\Database\Console\Migrations\BaseCommand */
        $command = app('command.migrate.refresh');

        /** @var \Symfony\Component\Console\Input\InputDefinition */
        $definition = $command->getDefinition();

        self::assertTrue($definition->hasOption('multi-databases'));
        self::assertTrue($definition->hasOption('only-default'));
        self::assertTrue($definition->hasOption('force'));

        self::assertFalse($definition->hasOption('all-contexts'));
        self::assertFalse($definition->hasOption('context-namespace'));
    }

    /**
     * @test
     * @testdox Check available options for the `migrate:reset` command
     */
    public function checkMigrateResetCommandOptions(): void
    {
        /** @var \Illuminate\Database\Console\Migrations\BaseCommand */
        $command = app('command.migrate.reset');

        /** @var \Symfony\Component\Console\Input\InputDefinition */
        $definition = $command->getDefinition();

        self::assertTrue($definition->hasOption('all-contexts'));
        self::assertTrue($definition->hasOption('multi-databases'));
        self::assertTrue($definition->hasOption('only-default'));
        self::assertTrue($definition->hasOption('force'));

        self::assertFalse($definition->hasOption('context-namespace'));
    }

    /**
     * @test
     * @testdox Check available options for the `migrate:rollback` command
     */
    public function checkMigrateRollbackCommandOptions(): void
    {
        /** @var \Illuminate\Database\Console\Migrations\BaseCommand */
        $command = app('command.migrate.rollback');

        /** @var \Symfony\Component\Console\Input\InputDefinition */
        $definition = $command->getDefinition();

        self::assertTrue($definition->hasOption('all-contexts'));
        self::assertTrue($definition->hasOption('multi-databases'));
        self::assertTrue($definition->hasOption('only-default'));
        self::assertTrue($definition->hasOption('force'));

        self::assertFalse($definition->hasOption('context-namespace'));
    }

    /**
     * @test
     * @testdox Check available options for the `migrate:status` command
     */
    public function checkMigrateStatusCommandOptions(): void
    {
        /** @var \Illuminate\Database\Console\Migrations\BaseCommand */
        $command = app('command.migrate.status');

        /** @var \Symfony\Component\Console\Input\InputDefinition */
        $definition = $command->getDefinition();

        self::assertTrue($definition->hasOption('all-contexts'));
        self::assertTrue($definition->hasOption('multi-databases'));
        self::assertTrue($definition->hasOption('only-default'));
        self::assertTrue($definition->hasOption('force'));

        self::assertFalse($definition->hasOption('context-namespace'));
    }

    /**
     * @test
     * @testdox Check available options for the `migrate` command
     */
    public function checkMigrateCommandOptions(): void
    {
        /** @var \Illuminate\Database\Console\Migrations\BaseCommand */
        $command = app('command.migrate');

        /** @var \Symfony\Component\Console\Input\InputDefinition */
        $definition = $command->getDefinition();

        self::assertTrue($definition->hasOption('all-contexts'));
        self::assertTrue($definition->hasOption('multi-databases'));
        self::assertTrue($definition->hasOption('only-default'));
        self::assertTrue($definition->hasOption('force'));

        self::assertFalse($definition->hasOption('context-namespace'));
    }

    /**
     * @test
     * @testdox Check available options for the `db:seed` command
     */
    public function checkSeedCommandOptions(): void
    {
        /** @var \Illuminate\Database\Console\Migrations\BaseCommand */
        $command = app('command.seed');

        /** @var \Symfony\Component\Console\Input\InputDefinition */
        $definition = $command->getDefinition();

        self::assertTrue($definition->hasOption('all-contexts'));
        self::assertTrue($definition->hasOption('only-default'));
        self::assertTrue($definition->hasOption('force'));

        self::assertFalse($definition->hasOption('context-namespace'));
        self::assertFalse($definition->hasOption('multi-databases'));
    }

    /**
     * @test
     * @dataProvider abstractsMakeCommandsProvider
     * @testdox Checking command options "$abstract"
     */
    public function makeCommandOptions(string $abstract): void
    {
        /** @var \Illuminate\Database\Console\Migrations\BaseCommand */
        $command = resolve($abstract);

        /** @var \Symfony\Component\Console\Input\InputDefinition */
        $definition = $command->getDefinition();

        self::assertTrue($definition->hasOption('context-namespace'));

        self::assertFalse($definition->hasOption('all-contexts'));
        self::assertFalse($definition->hasOption('multi-databases'));
        self::assertFalse($definition->hasOption('only-default'));
    }

    /**
     * Make command providers.
     *
     * @return iterable
     */
    public function abstractsMakeCommandsProvider(): iterable
    {
        $abstracts = [
            'command.cast.make',
            'command.channel.make',
            'command.console.make',
            'command.event.make',
            'command.exception.make',
            'command.factory.make',
            'command.job.make',
            'command.listener.make',
            'command.mail.make',
            'command.middleware.make',
            'command.model.make',
            'command.notification.make',
            'command.observer.make',
            'command.policy.make',
            'command.provider.make',
            'command.request.make',
            'command.resource.make',
            'command.rule.make',
            'command.seeder.make',
        ];

        foreach ($abstracts as $abstract) {
            yield [$abstract => $abstract];
        }
    }
}
