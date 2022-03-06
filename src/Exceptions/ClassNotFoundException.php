<?php

namespace Allyson\ArtisanDomainContext\Exceptions;

use RuntimeException;

/**
 * @codeCoverageIgnore
 */
class ClassNotFoundException extends RuntimeException
{
    private string $classname;

    public function __construct(string $classname)
    {
        parent::__construct("Class \"{$classname}\" does not exist");

        $this->classname = $classname;
    }

    public function getClassname(): string
    {
        return $this->classname;
    }
}
