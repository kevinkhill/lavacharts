<?php namespace Khill\Lavacharts\Tests;

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
