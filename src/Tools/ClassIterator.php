<?php

namespace Allyson\ArtisanDomainContext\Tools;

use Traversable;
use ArrayIterator;
use ReflectionClass;
use IteratorAggregate;
use Illuminate\Support\Str;
use PhpParser\ParserFactory;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use Symfony\Component\Finder\Finder;
use PhpParser\Error as PhpParserError;
use Symfony\Component\Finder\SplFileInfo;
use Allyson\ArtisanDomainContext\Exceptions\ReaderException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class ClassIterator implements IteratorAggregate
{
    /**
     * @phpstan-var array<class-string, \ReflectionClass>
     */
    private array $classes = [];

    private ?SplFileInfo $currentFile;

    public function __construct(private Finder $finder)
    {
        $this->customAutoloader();

        $this->handleFiles($this->finder);
    }

    /**
     * Get an iterator for the items.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->classes);
    }

    /**
     * Filters classes according to their type.
     * - Implements certain interface or is a child class.
     *
     * @param string $objectType
     *
     * @phpstan-return static<$this>
     */
    public function type(string $objectType): static
    {
        /** @var \ReflectionClass $reflectedClass */
        foreach ($this->classes as $qualifiedClassName => $reflectedClass) {
            if (! $reflectedClass->isSubclassOf($objectType)) {
                $this->removeClass($qualifiedClassName);
            }
        }

        return $this;
    }

    /**
     * Filters classes by name.
     *
     * @param string $pattern
     *
     * @phpstan-return static<$this>
     */
    public function name(string $pattern): static
    {
        /** @phpstan-var array<class-string> */
        $qualifiedClassNames = array_keys($this->classes);

        foreach ($qualifiedClassNames as $className) {
            $subject = $className;
            $partsOfClassName = explode('\\', $className);
            $subject = array_pop($partsOfClassName);

            if (empty(Str::match($pattern, $subject))) {
                $this->removeClass($className);
            }
        }

        return $this;
    }

    /**
     * Filters classes by fully qualified namespace.
     *
     * @param string $namespace
     *
     * @phpstan-return static<$this>
     */
    public function inNamespace(string $namespace): static
    {
        /** @var \ReflectionClass $reflectedClass */
        foreach ($this->classes as $qualifiedClassName => $reflectedClass) {
            $pattern = '/^' . preg_quote($namespace) . '/';

            if (empty(Str::match($pattern, $reflectedClass->getNamespaceName()))) {
                $this->removeClass($qualifiedClassName);
            }
        }

        return $this;
    }

    /**
     * Register given function as __autoload() implementation.
     *
     * @return void
     */
    private function customAutoloader(): void
    {
        // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
        spl_autoload_register(function ($className) {
            if (! empty($this->currentFile)) {
                require_once $this->currentFile->getRealPath();
            }
        });
    }

    /**
     * Manipulate files to retrieve class/type information.
     *
     * @param \Symfony\Component\Finder\Finder $finder
     *
     * @return void
     *
     * @throws \Allyson\ArtisanDomainContext\Exceptions\ReaderException
     */
    private function handleFiles(Finder $finder): void
    {
        $parserFactory = new ParserFactory();

        /** @var \Symfony\Component\Finder\SplFileInfo $splFileInfo */
        foreach ($finder as $splFileInfo) {
            $this->currentFile = $splFileInfo;

            /** @var \PhpParser\Parser\Multiple */
            $parser = $parserFactory->create(ParserFactory::PREFER_PHP7);

            try {
                /** @var \PhpParser\Node\Stmt[] */
                $ast = $parser->parse($splFileInfo->getContents());

                $this->parseFile($ast);
            } catch (PhpParserError $error) { // @codeCoverageIgnore
                throw new ReaderException("Parse error: {$error->getMessage()}"); // @codeCoverageIgnore
            } finally {
                $this->currentFile = null;
            }
        }
    }

    /**
     * @param \PhpParser\Node\Stmt[] $ast
     *
     * @return void
     */
    private function parseFile(array $ast): void
    {
        /** @var \PhpParser\Node\Stmt $nodeStmt */
        foreach ($ast as $nodeStmt) {
            if ($nodeStmt instanceof Namespace_) {
                /** @var \PhpParser\Node\Name */
                $nodeStmtName = $nodeStmt->name;

                $classNamespace = $nodeStmtName->toString();

                $this->handleClasses($nodeStmt->stmts, $classNamespace);
            }
        }
    }

    /**
     * @param \PhpParser\Node\Stmt[] $stmts
     * @param string $classNamespace
     *
     * @return void
     */
    private function handleClasses(array $stmts, string $classNamespace): void
    {
        foreach ($stmts as $nodeClass) {
            $allowedNodes = $nodeClass instanceof Class_ ||
                            $nodeClass instanceof Interface_ ||
                            $nodeClass instanceof Trait_;

            if ($allowedNodes) {
                /** @var \PhpParser\Node\Identifier */
                $stmtClass = $nodeClass->name;

                // Remove leading backslashes - So that it is not a fully qualified class!
                /** @phpstan-var class-string */
                $qualifiedClassName = ltrim("{$classNamespace}\\{$stmtClass->name}", '\\');

                $class = new ReflectionClass($qualifiedClassName);

                $this->classes[$qualifiedClassName] = $class;
            }
        }
    }

    /**
     * Remove class from class list.
     *
     * @param string $className
     *
     * @return void
     */
    private function removeClass(string $className): void
    {
        unset($this->classes[$className]);
    }
}
