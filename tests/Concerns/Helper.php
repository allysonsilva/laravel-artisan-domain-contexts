<?php

namespace Allyson\ArtisanDomainContext\Tests\Concerns;

use ReflectionClass;
use RuntimeException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;
use Illuminate\Contracts\Console\Kernel;
use Allyson\ArtisanDomainContext\Tools\ClassIterator;

trait Helper
{
    /**
     * Run artisan in a php process using `proc_open`.
     *
     * @param string[] $command
     *
     * @example $this->runArtisan('db:seed', '--xyz')->getOutput()
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function runArtisan(string ...$command): Process
    {
        $artisan = "{$this->getBasePath()}/artisan";

        $process = new Process(['php', $artisan, ...$command]);
        $process->run();

        // $process = Process::fromShellCommandline('echo "1, 3" | php "$ARTISAN_BIN" migrate --context=User');
        // $process->run(null, ['ARTISAN_BIN' => $artisan]);

        // $command = escapeshellcmd("{$this->getBasePath()}/artisan");

        // exec(
        //     sprintf(
        //         '%s %s %s --context=User',
        //         escapeshellarg(PHP_BINARY),
        //         $command,
        //         'migrate'
        //     ),
        //     $dataResult
        // );

        return $process;
    }

    /**
     * Retrieve the class from a given directory path.
     *
     * @param string $path
     * @param string $folder
     *
     * @return \ReflectionClass
     */
    protected function getClass(string $className, string $path): ReflectionClass
    {
        $finder = Finder::create()
                      ->in($path)
                      ->depth(0)
                      ->name("/{$className}\.php\$/i")
                      ->files();

        $classIterator = new ClassIterator($finder);

        return Arr::first($classIterator);
    }

    /**
     * Retrieve context component class.
     *
     * @param string $className
     * @param string $componentFolder
     * @param string|null $contextFolder
     *
     * @return array<\ReflectionClass, string>
     */
    protected function getContextComponentClass(string $className, string $componentFolder, ?string $contextFolder = null): array
    {
        $contextComponentPath = $this->getContextComponentPath($componentFolder, $contextFolder);
        $componentFilepath = $this->getComponentFilepath($className, $componentFolder, $contextFolder);

        if (! File::exists($componentFilepath)) {
            throw new RuntimeException(sprintf('File "%s" cannot be found in the "%s" directory', "{$className}.php", $contextComponentPath));
        }

        return [$this->getClass($className, $contextComponentPath), $componentFilepath];
    }

    /**
     * Retrieve context component path.
     *
     * @param string $componentFolder
     * @param string|null $contextFolder
     *
     * @return string
     */
    protected function getContextComponentPath(string $componentFolder, ?string $contextFolder = null): string
    {
        return $this->applicationDomainPath .'/'. ($contextFolder ?? $this->contextFolder) .'/'. $componentFolder;
    }

    /**
     * Retrieves full path of component file.
     *
     * @param string $className
     * @param string $componentFolder
     * @param string|null $contextFolder
     *
     * @return string
     */
    protected function getComponentFilepath(string $className, string $componentFolder, ?string $contextFolder = null): string
    {
        $contextComponentPath = $this->getContextComponentPath($componentFolder, $contextFolder);
        $componentFilepath = $contextComponentPath .'/'. $className;

        if (empty(pathinfo($componentFilepath, PATHINFO_EXTENSION))) {
            $componentFilepath = $componentFilepath . '.php';
        }

        return $componentFilepath;
    }

    /**
     * Generate a random string.
     *
     * @param int $length
     *
     * @return string
     */
    protected function randomString(int $length = 10): string
    {
        return Str::random($length);
    }

    /**
     * Retrieves the namespace of a given domain component.
     *
     * @param string $componentFolder
     * @param string|null $contextFolder
     *
     * @return string
     */
    protected function getDomainComponentNamespace(string $componentFolder, ?string $contextFolder = null): string
    {
        $rootNamespace = trim($this->app->getNamespace(), '\\');

        return $rootNamespace .'\\'. $this->domainFolder .'\\'. ($contextFolder ?? $this->contextFolder) .'\\'. str_replace('/', '\\', $componentFolder);
    }

    /**
     * Retrieve the component's custom namespace.
     *
     * @param string $contextNamespace
     * @param string $componentFolder
     *
     * @return string
     */
    protected function getDomainComponentCustomNamespace(string $contextNamespace, string $componentFolder): string
    {
        return trim($contextNamespace, '\\') .'\\'. str_replace('/', '\\', $componentFolder);
    }

    /**
     * Assert to check if the expected string is in the command output.
     *
     * @param string $expectedText
     *
     * @return void
     */
    protected function seeInConsoleOutput(string $expectedText): void
    {
        $consoleOutput = $this->app[Kernel::class]->output();

        $this->assertStringContainsString(
            $expectedText,
            $consoleOutput,
            "Did not see `{$expectedText}` in console output: `$consoleOutput`"
        );
    }

    /**
     * Assert to check whether a given string is not in the command output.
     *
     * @param string $unexpectedText
     *
     * @return void
     */
    protected function doNotSeeInConsoleOutput(string $unexpectedText): void
    {
        $consoleOutput = $this->app[Kernel::class]->output();

        $this->assertNotContains(
            $unexpectedText,
            $consoleOutput,
            "Did not expect to see `{$unexpectedText}` in console output: `$consoleOutput`"
        );
    }
}
