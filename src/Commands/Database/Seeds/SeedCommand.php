<?php

namespace Allyson\ArtisanDomainContext\Commands\Database\Seeds;

use Illuminate\Database\Seeder;
use Allyson\ArtisanDomainContext\Tools\ClassIterator;
use Allyson\ArtisanDomainContext\Exceptions\ClassNotFoundException;
use Allyson\ArtisanDomainContext\Commands\Concerns\InteractsWithChoices;
use Illuminate\Database\Console\Seeds\SeedCommand as LaravelSeedCommand;

class SeedCommand extends LaravelSeedCommand
{
    use InteractsWithChoices;

    /**
     * Retrieves the name of the folder containing the files.
     *
     * @return string
     */
    protected function getContextComponentFolder(): string
    {
        return config('context.folders.components.seeders');
    }

    /**
     * Handles the `ClassIterator` class for filtering reflection classes.
     *
     * @param \Allyson\ArtisanDomainContext\Tools\ClassIterator<class-string> $classIterator
     *
     * @return \Allyson\ArtisanDomainContext\Tools\ClassIterator<class-string>
     */
    protected function handleClassIterator(ClassIterator $classIterator): ClassIterator
    {
        return $classIterator->type(Seeder::class);
    }

    /**
     * Handles the `Finder` class for filtering files.
     *
     * @param \Symfony\Component\Finder\Finder $finder
     *
     * @return void
     */
    // protected function decorateFinder(Finder $finder): void
    // {
    //     $finder->name('/Seeder\.php$/i')->sortByName(true);
    // }

    /**
     * Get a seeder instance from the container.
     *
     * @return \Illuminate\Database\Seeder|object
     */
    protected function getSeeder() /** @phpstan-ignore-line */
    {
        if ($this->hasOnlyDefaultOption()) {
            return parent::getSeeder();
        }

        /** @var \Illuminate\Support\Collection<\ReflectionClass> */
        $seedersClasses = $this->getClassesFromContext();

        /** @var array<class-string> */
        $seedersClasses = $this->transformReflectionClassIntoArrayOfClassNames($seedersClasses);

        /** @var array<class-string<\Illuminate\Database\Seeder>> */
        $chosen = $this->choose($seedersClasses);

        $handlerSeedersClasses = $this->dummyClass();

        /** @phpstan-ignore-next-line */
        $handlerSeedersClasses->setSeedersClasses($chosen);

        return $handlerSeedersClasses;
    }

    /**
     * Get the console command options.
     *
     * @return array
     *
     * @phpstan-return InputOptionsArray
     */
    protected function getOptions(): array
    {
        $options = parent::getOptions();

        array_push($options, ...$this->withAllFiltersOption());

        return $options;
    }

    /**
     * @phpstan-ignore-next-line
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private function dummyClass(): object
    {
        return new class ($this) {
            /**
             * @var array<class-string<\Illuminate\Database\Seeder>>
             */
            private array $seedersClasses;

            public function __construct(private SeedCommand $command)
            {
            }

            /**
             * @param string[] $seedersClasses
             *
             * @phpstan-param array<class-string<\Illuminate\Database\Seeder>> $seedersClasses
             */
            public function setSeedersClasses(array $seedersClasses): void
            {
                $this->seedersClasses = $seedersClasses;
            }

            public function __invoke(): void
            {
                /** @var string&class-string $seederClass */
                foreach ($this->seedersClasses as $seederClass) {
                    if (! class_exists($seederClass)) {
                        throw new ClassNotFoundException($seederClass);
                    }

                    /** @phpstan-ignore-next-line */
                    $this->command
                            ->getLaravel()
                            ->make($seederClass)
                            ->setContainer($this->command->getLaravel())
                            ->setCommand($this->command)
                            ->__invoke();
                }
            }
        };
    }
}
