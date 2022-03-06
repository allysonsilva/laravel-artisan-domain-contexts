<?php

namespace Allyson\ArtisanDomainContext\Tests\Overrides;

use Allyson\ArtisanDomainContext\Tests\Concerns\SetUpBefore;
use Orchestra\Testbench\Console\Commander as TestbenchCommander;

class Commander extends TestbenchCommander
{
    use SetUpBefore;

    /**
     * Create symlink on vendor path.
     * suppressing: Warning: symlink(): File exists in ...
     */
    protected function createSymlinkToVendorPath(): void
    {
        return;
    }
}
