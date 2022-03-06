<?php

namespace Allyson\ArtisanDomainContext\Commands\Concerns;

use Generator;
use ReflectionClass;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Symfony\Component\Finder\Finder;
use Allyson\ArtisanDomainContext\Tools\ClassIterator;

trait InteractsWithChoices
{
    use CustomOptionsFilters;

    /**
     * Retrieves the name of the context component folder that contains the files.
     *
     * @return string
     */
    abstract protected function getContextComponentFolder(): string;

    /**
     * Handles the `Finder` class for filtering files.
     *
     * @return \Symfony\Component\Finder\Finder
     */
    protected function handleFinder(): Finder
    {
        // Remove unnecessary slashes from a given path
        $contextComponentPath = Str::of($this->getContextDir())->replaceMatches('#/+#', '/');

        $finder = new Finder();
        $finder->files()->in($contextComponentPath);

        $this->decorateFinder($finder);

        return $finder;
    }

    /**
     * Compose/decorate the finder object.
     *
     * @param \Symfony\Component\Finder\Finder $finder
     *
     * @return void
     */
    protected function decorateFinder(Finder $finder): void
    {
        $finder->name('/\.php$/i')
               ->size('< 100K')
               ->sortByName(true);
    }

    /**
     * Recovers the folder where the files or classes are located.
     *
     * @return string
     */
    protected function getContextDir(): string
    {
        return app_path() . '/' .
                config('context.folders.domain') . '/' .
                strval($this->option('context')) . '/' .
                config('context.folders.pattern') .
                $this->getContextComponentFolder();
    }

    /**
     * Retrieve files from given context or all contexts.
     *
     * @return \Generator<string>
     */
    protected function getFilesFromContext(): Generator
    {
        /** @var \Symfony\Component\Finder\Finder $files */
        $files = $this->handleFinder();

        /** @var \Symfony\Component\Finder\SplFileInfo $splFileInfo */
        foreach ($files as $splFileInfo) {
            $pathPrefixToBeRemoved = base_path() . '/';
            $absolutePathFile = strval($splFileInfo->getRealPath());
            $relativePathFile = str_replace($pathPrefixToBeRemoved, '', $absolutePathFile);

            yield $relativePathFile;
        }

        yield from [];
    }

    /**
     * Retrieves all classes for a given context via the `--context` option,
     * or all classes for all contexts if the option is not passed.
     *
     * @return \Illuminate\Support\Collection<\ReflectionClass>
     */
    protected function getClassesFromContext(): Collection
    {
        /** @var \Allyson\ArtisanDomainContext\Tools\ClassIterator<class-string> $classIterator */
        $classIterator = new ClassIterator($this->handleFinder());

        /** @phpstan-var array<class-string, \ReflectionClass> */
        $classMap = iterator_to_array($classIterator);

        /** @phpstan-ignore-next-line */
        if (method_exists($this, 'handleClassIterator')) {
            $classMap = iterator_to_array($this->handleClassIterator($classIterator));
        }

        /** @var \ReflectionClass[] */
        $classes = array_values($classMap);

        return collect($classes);
    }

    /**
     * Transform the files reflection classes into an array
     * containing the full class name (with namespace).
     *
     * @param \Illuminate\Support\Collection<\ReflectionClass> $classes
     *
     * @return array<string>
     */
    protected function transformReflectionClassIntoArrayOfClassNames(Collection $classes): array
    {
        return $classes->sortBy(fn (ReflectionClass $class) => $class->getShortName())
                       ->map(fn (ReflectionClass $class) => $class->getName())
                       ->values()
                       ->all();
    }

    /**
     * List and choose which classes/files to run.
     *
     * @param array<class-string>|\Generator<string> $choices
     *
     * @return string[]|string
     */
    protected function choose(array|Generator $choices): string|array
    {
        if ($choices instanceof Generator) {
            $choices = iterator_to_array($choices);
        }

        if ($this->hasForceOrAllContextsOption()) {
            return $choices;
        }

        array_unshift($choices, 'ALL');

        $chosen = Arr::wrap($this->choice(
            'Which class/file would you like to run?',
            $choices,
            multiple: true,
        ));

        if (in_array('ALL', $chosen, true)) {
            array_shift($choices);
            $chosen = $choices;
        }

        return array_values($chosen);
    }

    /**
     * @param array<string> $choices
     *
     * @return array<string>
     */
    protected function addAbsolutePath(array $choices): array
    {
        $basePath = base_path();

        /** @var array<string> */
        $choicesWithAbsolutePath = [];

        foreach ($choices as $choice) {
            if (! str_starts_with($choice, $basePath)) {
                $choicesWithAbsolutePath[] = base_path($choice);
            }
        }

        return $choicesWithAbsolutePath;
    }

    /**
     * Checks if all choices should be selected.
     * Laravel's `choice` command will not be applied!
     *
     * @return bool
     */
    protected function hasForceOrAllContextsOption(): bool
    {
        $optionName = 'force';

        return ($this->hasOption($optionName) && boolval($this->option($optionName))) || $this->hasAllContextsOption();
    }
}
