<?php

namespace Allyson\ArtisanDomainContext\Tests\Unit\Inputs;

use Allyson\ArtisanDomainContext\Inputs\Host;
use Allyson\ArtisanDomainContext\Inputs\Tenant;
use Allyson\ArtisanDomainContext\Inputs\Context;
use Allyson\ArtisanDomainContext\Tests\TestCase;
use Symfony\Component\Console\Input\InputDefinition;

/**
 * @group Input
 */
class InputOptionsTest extends TestCase
{
    /**
     * @test
     */
    public function testCLIInputOptions()
    {
        $definition = new InputDefinition([]);

        $contextInput = (new Context($definition));
        $contextInput->execute();

        (new Host($definition))->execute();
        (new Tenant($definition))->execute();

        self::assertTrue($definition->hasOption('context'));
        self::assertTrue($definition->hasOption('host'));
        self::assertTrue($definition->hasOption('tenant'));
        self::assertTrue($contextInput->getOption('context')->equals($contextInput->withOption()));
    }
}
