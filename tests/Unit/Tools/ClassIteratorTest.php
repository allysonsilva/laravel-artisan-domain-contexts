<?php

namespace Allyson\ArtisanDomainContext\Tests\Unit\Tools;

use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Allyson\ArtisanDomainContext\Tests\TestCase;
use Allyson\ArtisanDomainContext\Tools\ClassIterator;
use Allyson\ArtisanDomainContext\Tests\Unit\Tools\Classes\BarClassA;
use Allyson\ArtisanDomainContext\Tests\Unit\Tools\Classes\BarClassB;
use Allyson\ArtisanDomainContext\Tests\Unit\Tools\Classes\BarClassC;
use Allyson\ArtisanDomainContext\Tests\Unit\Tools\Classes\FooClassA;
use Allyson\ArtisanDomainContext\Tests\Unit\Tools\Classes\FooClassB;
use Allyson\ArtisanDomainContext\Tests\Unit\Tools\Classes\FooClassC;
use Allyson\ArtisanDomainContext\Tests\Unit\Tools\Classes\BarInterface;
use Allyson\ArtisanDomainContext\Tests\Unit\Tools\Classes\FooInterface;
use Allyson\ArtisanDomainContext\Tests\Unit\Tools\Classes\SharedInterface;

/**
 * @group Tools
 */
class ClassIteratorTest extends TestCase
{
    private string $context = 'User';

    private static string $path;

    /**
     * This method is called before the first test of this test class is run.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        static::$path = $path = realpath(__DIR__ . '/Classes/');

        $finderStub = Finder::create()
                            ->in($path)
                            ->depth(0)
                            ->name("/\.stub\$/i")
                            ->files();

        foreach ($finderStub as $splFileInfo) {
            $filename = $splFileInfo->getBasename('.' . $splFileInfo->getExtension()) . '.php';
            $target = $splFileInfo->getPath() . '/' . $filename;

            copy($splFileInfo->getRealPath(), $target);
        }
    }

    public function testTypeFilter(): void
    {
        /** @var \ReflectionClass $reflectedClass */
        foreach ($this->getClasses()->type(BarInterface::class) as $reflectedClass) {
            self::assertTrue($reflectedClass->isSubclassOf(BarInterface::class));
        }

        /** @var \ReflectionClass $reflectedClass */
        foreach ($this->getClasses()->type(FooInterface::class) as $reflectedClass) {
            self::assertTrue($reflectedClass->isSubclassOf(FooInterface::class));
        }

        /** @var \ReflectionClass $reflectedClass */
        foreach ($this->getClasses()->type(SharedInterface::class) as $reflectedClass) {
            self::assertTrue($reflectedClass->isSubclassOf(SharedInterface::class));
        }
    }

    public function testBarTypeFilter(): void
    {
        /** @var \ReflectionClass[] */
        $barTypeClasses = array_values(iterator_to_array($this->getClasses()->type(BarClassA::class)));

        self::assertCount(2, $barTypeClasses);

        self::assertTrue($barTypeClasses[0]->isSubclassOf(BarClassA::class));
        self::assertTrue($barTypeClasses[0]->getName() === BarClassB::class);
        self::assertTrue($barTypeClasses[1]->isSubclassOf(BarClassA::class));
        self::assertTrue($barTypeClasses[1]->getName() === BarClassC::class);
    }

    public function testNameFilter(): void
    {
        $fooClasses = array_values(iterator_to_array($this->getClasses()->name('/FooClass/i')));

        self::assertCount(3, $fooClasses);

        self::assertTrue($fooClasses[0]->getName() === FooClassA::class);
        self::assertTrue($fooClasses[1]->getName() === FooClassB::class);
        self::assertTrue($fooClasses[2]->getName() === FooClassC::class);
    }

    public function testInNamespaceFilter(): void
    {
        $i = 1;

        while ($i <= 3) {
            $i++;

            $this->artisan('make:job', ['--context' => $this->context, 'name' => "Baz{$this->randomString()}Job",])
                ->assertSuccessful()
                ->expectsOutput('Job created successfully.');
        }

        $jobContextPath = $this->getContextComponentPath(config('context.folders.components.jobs'), $this->context);

        /** @var \ReflectionClass[] */
        $jobsClasses = array_values(iterator_to_array($this->getClasses($jobContextPath)->inNamespace(\App\Domain\User\Jobs::class)));

        foreach ($jobsClasses as $class) {
            self::assertTrue($class->implementsInterface(ShouldQueue::class));
        }

        self::assertCount(3, $jobsClasses);
        self::assertEmpty(array_values(iterator_to_array($this->getClasses($jobContextPath)->inNamespace(\App\Domain\User\Models::class))));

        $classes = $this->getClasses()->inNamespace(Classes::class);

        self::assertCount(9, $classes);

        File::deleteDirectory($jobContextPath);
    }

    /**
     * @test It must be possible to load and manipulate the created class at runtime
     * @testdox A "factory" must be created in the context folder of the --context option
     */
    public function loadClassWithCustomNamespace(): void
    {
        $jobName = "Bar{$this->randomString()}Job";

        $this->artisan('make:job', [
                '--context' => $this->context,
                '--context-namespace' => $contextNamespace = $this->context . 'Context',
                'name' => $jobName,
            ])
            ->assertSuccessful()
            ->expectsOutput('Job created successfully.');

        /** @var string */
        $componentFolder = config('context.folders.components.jobs');

        /**
         * @var \ReflectionClass $class
         * @var string $file
         */
        [$class, $file] = $this->getContextComponentClass($jobName, $componentFolder, $this->context);

        File::delete($file);

        $this->assertSame($class->getShortName(), $jobName);
        $this->assertSame($class->getNamespaceName(), $this->getDomainComponentCustomNamespace($contextNamespace, $componentFolder));
    }

    /**
     * Retrieves the instance of the classes used as dummy.
     *
     * @param string|null $path
     *
     * @return \Allyson\ArtisanDomainContext\Tools\ClassIterator
     */
    private function getClasses(?string $path = null): ClassIterator
    {
        $finder = Finder::create()
                        ->in($path ?? static::$path)
                        ->name("/\.php\$/i")
                        ->files()
                        ->sortByName();

        $classes = new ClassIterator($finder);

        // self::assertCount(9, $classes);

        return $classes;
    }
}
