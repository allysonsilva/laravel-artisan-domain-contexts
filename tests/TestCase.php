<?php

namespace Allyson\ArtisanDomainContext\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Allyson\ArtisanDomainContext\Tests\Concerns\Helper;
use Allyson\ArtisanDomainContext\Tests\Concerns\SetUpBefore;

class TestCase extends Orchestra
{
    use Helper;
    use SetUpBefore;

    /**
     * Load Environment variables.
     *
     * @var bool
     */
    protected $loadEnvironmentVariables = false;

    /**
     * Application APP DOMAIN/CONTEXTs folder path.
     *
     * @var string
     */
    protected string $applicationDomainPath;

    /**
     * Default context folder name.
     *
     * @return string
     */
    protected string $contextFolder = 'Post';

    /**
     * Name of the folder where the domain modules/contexts are located.
     *
     * @var string
     */
    protected string $domainFolder;

    /**
     * This method is called before the first test of this test class is run.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        //
    }

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->afterApplicationRefreshed(function (): void {
            if (! file_exists("{$this->getBasePath()}/config/context.php")) {
                $this->artisan('vendor:publish', [
                    '--tag' => 'context-config',
                    '--force' => true
                ])->assertSuccessful();
            }
        });

        static::resolveSetUpBefore();

        // Code before application created.
        parent::setUp();
        // Code after application created.

        $this->applicationDomainPath = static::$applicationPath . '/app/' . config('context.folders.domain');

        $this->domainFolder = config('context.folders.domain');
    }

    /**
     * This method is called after each test.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        //
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        // $app['config']->set('database.default', 'testbench');

        // $app['config']->set('database.connections.testbench', [
        //     'driver'   => 'sqlite',
        //     'database' => ':memory:',
        //     'prefix'   => '',
        // ]);
    }
}
