<?php

namespace Allyson\ArtisanDomainContext\Tests\Unit\Commands\Foundation;

use Closure;
use Mockery;
use Illuminate\Support\Facades\File;
use Allyson\ArtisanDomainContext\Tests\Unit\MakeCommandTestCase;

/**
 * @group Making
 */
class MakeCommandTest extends MakeCommandTestCase
{
    public function makeCommandsProvider(): array
    {
        return [
            'Testing make:cast command'  => [
                'command' => [
                    'name' => 'make:cast',
                    'return' => Mockery::pattern('/Cast (?:.+) created successfully/'),
                ],
                'dummyClass' => "Foo{$this->randomString()}Cast",
                'componentFolder' => fn () => config('context.folders.components.casts'),
                'laravel' => [
                    'folder' => fn () => app_path('Casts'),
                    'namespace' => 'App\\Casts',
                ],
            ],

            'Testing make:channel command'  => [
                'command' => [
                    'name' => 'make:channel',
                    'return' => Mockery::pattern('/Channel (?:.+) created successfully/'),
                ],
                'dummyClass' => "Foo{$this->randomString()}Channel",
                'componentFolder' => fn () => config('context.folders.components.channel'),
                'laravel' => [
                    'folder' => fn () => app_path('Broadcasting'),
                    'namespace' => 'App\\Broadcasting',
                ],
            ],

            'Testing make:command command'  => [
                'command' => [
                    'name' => 'make:command',
                    'return' => Mockery::pattern('/Console command (?:.+) created successfully/'),
                ],
                'dummyClass' => "Foo{$this->randomString()}Command",
                'componentFolder' => fn () => config('context.folders.components.console'),
                'laravel' => [
                    'folder' => fn () => app_path('Console/Commands'),
                    'namespace' => 'App\\Console\\Commands',
                ],
            ],

            'Testing make:event command'  => [
                'command' => [
                    'name' => 'make:event',
                    'return' => Mockery::pattern('/Event (?:.+) created successfully/'),
                ],
                'dummyClass' => "Foo{$this->randomString()}Event",
                'componentFolder' => fn () => config('context.folders.components.events'),
                'laravel' => [
                    'folder' => fn () => app_path('Events'),
                    'namespace' => 'App\\Events',
                ],
            ],

            'Testing make:exception command'  => [
                'command' => [
                    'name' => 'make:exception',
                    'return' => Mockery::pattern('/Exception (?:.+) created successfully/'),
                ],
                'dummyClass' => "Foo{$this->randomString()}Exception",
                'componentFolder' => fn () => config('context.folders.components.exceptions'),
                'laravel' => [
                    'folder' => fn () => app_path('Exceptions'),
                    'namespace' => 'App\\Exceptions',
                ],
            ],

            'Testing make:job command'  => [
                'command' => [
                    'name' => 'make:job',
                    'return' => Mockery::pattern('/Job (?:.+) created successfully/'),
                ],
                'dummyClass' => "Foo{$this->randomString()}Job",
                'componentFolder' => fn () => config('context.folders.components.jobs'),
                'laravel' => [
                    'folder' => fn () => app_path('Jobs'),
                    'namespace' => 'App\\Jobs',
                ],
            ],

            'Testing make:listener command'  => [
                'command' => [
                    'name' => 'make:listener',
                    'return' => Mockery::pattern('/Listener (?:.+) created successfully/'),
                ],
                'dummyClass' => "Foo{$this->randomString()}Listener",
                'componentFolder' => fn () => config('context.folders.components.listeners'),
                'laravel' => [
                    'folder' => fn () => app_path('Listeners'),
                    'namespace' => 'App\\Listeners',
                ],
            ],

            'Testing make:mail command'  => [
                'command' => [
                    'name' => 'make:mail',
                    'return' => Mockery::pattern('/Mail (?:.+) created successfully/'),
                ],
                'dummyClass' => "Foo{$this->randomString()}Mail",
                'componentFolder' => fn () => config('context.folders.components.mail'),
                'laravel' => [
                    'folder' => fn () => app_path('Mail'),
                    'namespace' => 'App\\Mail',
                ],
            ],

            'Testing make:notification command'  => [
                'command' => [
                    'name' => 'make:notification',
                    'return' => Mockery::pattern('/Notification (?:.+) created successfully/'),
                ],
                'dummyClass' => "Foo{$this->randomString()}Notification",
                'componentFolder' => fn () => config('context.folders.components.notifications'),
                'laravel' => [
                    'folder' => fn () => app_path('Notifications'),
                    'namespace' => 'App\\Notifications',
                ],
            ],

            'Testing make:observer command'  => [
                'command' => [
                    'name' => 'make:observer',
                    'return' => Mockery::pattern('/Observer (?:.+) created successfully/'),
                ],
                'dummyClass' => "Foo{$this->randomString()}Observer",
                'componentFolder' => fn () => config('context.folders.components.observers'),
                'laravel' => [
                    'folder' => fn () => app_path('Observers'),
                    'namespace' => 'App\\Observers',
                ],
            ],

            'Testing make:policy command'  => [
                'command' => [
                    'name' => 'make:policy',
                    'return' => Mockery::pattern('/Policy (?:.+) created successfully/'),
                ],
                'dummyClass' => "Foo{$this->randomString()}Policy",
                'componentFolder' => fn () => config('context.folders.components.policies'),
                'laravel' => [
                    'folder' => fn () => app_path('Policies'),
                    'namespace' => 'App\\Policies',
                ],
            ],

            'Testing make:provider command'  => [
                'command' => [
                    'name' => 'make:provider',
                    'return' => Mockery::pattern('/Provider (?:.+) created successfully/'),
                ],
                'dummyClass' => "Foo{$this->randomString()}Provider",
                'componentFolder' => fn () => config('context.folders.components.providers'),
                'laravel' => [
                    'folder' => fn () => app_path('Providers'),
                    'namespace' => 'App\\Providers',
                ],
            ],

            'Testing make:request command'  => [
                'command' => [
                    'name' => 'make:request',
                    'return' => Mockery::pattern('/Request (?:.+) created successfully/'),
                ],
                'dummyClass' => "Foo{$this->randomString()}Request",
                'componentFolder' => fn () => config('context.folders.components.requests'),
                'laravel' => [
                    'folder' => fn () => app_path('Http/Requests'),
                    'namespace' => 'App\\Http\\Requests',
                ],
            ],

            'Testing make:resource command'  => [
                'command' => [
                    'name' => 'make:resource',
                    'return' => Mockery::pattern('/Resource (?:.+) created successfully/'),
                ],
                'dummyClass' => "Foo{$this->randomString()}Resource",
                'componentFolder' => fn () => config('context.folders.components.resources'),
                'laravel' => [
                    'folder' => fn () => app_path('Http/Resources'),
                    'namespace' => 'App\\Http\\Resources',
                ],
            ],

            'Testing make:rule command'  => [
                'command' => [
                    'name' => 'make:rule',
                    'return' => Mockery::pattern('/Rule (?:.+) created successfully/'),
                ],
                'dummyClass' => "Foo{$this->randomString()}Rule",
                'componentFolder' => fn () => config('context.folders.components.rules'),
                'laravel' => [
                    'folder' => fn () => app_path('Rules'),
                    'namespace' => 'App\\Rules',
                ],
            ],

            'Testing make:middleware command'  => [
                'command' => [
                    'name' => 'make:middleware',
                    'return' => Mockery::pattern('/Middleware (?:.+) created successfully/'),
                ],
                'dummyClass' => "Foo{$this->randomString()}Middleware",
                'componentFolder' => fn () => config('context.folders.components.middlewares'),
                'laravel' => [
                    'folder' => fn () => app_path('Http/Middleware'),
                    'namespace' => 'App\\Http\\Middleware',
                ],
            ],
        ];
    }

    /**
     * @test
     * @testdox The "command" must be created in the context folder of the --context option
     * @dataProvider makeCommandsProvider
     */
    public function usingContextOptionWhenCreatingTheCommand(array $command, string $className, Closure $componentFolder): void
    {
        $componentFolder = $componentFolder();

        $this->artisan($command['name'], ['--context' => $this->contextFolder, 'name' => $className])
             ->assertSuccessful()
             ->expectsOutput($command['return']);

        /** @var \ReflectionClass $class */
        [$class, $file] = $this->getContextComponentClass($className, $componentFolder);

        File::deleteDirectory(dirname($file));

        $this->assertSame($class->getShortName(), $className);
        $this->assertSame($class->getNamespaceName(), $this->getDomainComponentNamespace($componentFolder));
    }

    /**
     * @test
     * @testdox When the --context option is not present in the command, then the command should act in the standard Laravel way
     * @dataProvider makeCommandsProvider
     */
    public function savingCommandInLaravelDefaultFolder(array $command, string $className, Closure $componentFolder, array $laravel): void
    {
        $this->artisan($command['name'], ['name' => $className])
             ->assertSuccessful()
             ->expectsOutput($command['return']);

        $class = $this->getClass($className, $laravelFolder = $laravel['folder']());

        File::deleteDirectory($laravelFolder);

        $this->assertSame($class->getShortName(), $className);
        $this->assertSame($class->getNamespaceName(), $laravel['namespace']);
    }

    /**
     * @test
     * @testdox Test using the `--context-namespace` option when creating the command
     * @dataProvider makeCommandsProvider
     */
    public function usingCustomNamespaceOptionWhenCreatingTheCommand(array $command, string $className, Closure $componentFolder): void
    {
        $contextNamespace = $this->contextFolder . 'Context';
        $componentFolder = $componentFolder();

        $this->artisan($command['name'], [
                        '--context' => $this->contextFolder,
                        '--context-namespace' => $contextNamespace,
                        'name' => $className])
             ->assertSuccessful()
             ->expectsOutput($command['return']);

        /** @var \ReflectionClass $class */
        [$class, $file] = $this->getContextComponentClass($className, $componentFolder);

        $componentCustomNamespace = $this->getDomainComponentCustomNamespace($contextNamespace, $componentFolder);

        File::deleteDirectory(dirname($file));

        $this->assertSame($class->getShortName(), $className);
        $this->assertSame($class->getNamespaceName(), $componentCustomNamespace);
    }
}
