<?php namespace Khill\Lavacharts\Tests;

use Khill\Lavacharts\Configs as Config;

class TestCase extends \Orchestra\Testbench\TestCase {

    protected function getPackageProviders()
    {
        return array('Khill\Lavacharts\LavachartsServiceProvider');
    }

    protected function getPackageAliases()
    {
        return array('Lava' => 'Khill\Lavacharts\Facades\Lavacharts');
    }

}
