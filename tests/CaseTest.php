<?php

class CaseTest extends PHPUnit_Framework_TestCase {

    protected function getPackageProviders()
    {
        return array('Khill\Lavacharts\LavachartsServiceProvider');
    }

    protected function getPackageAliases()
    {
        return array('Lava' => 'Khill\Lavacharts\Facades\Lavacharts');
    }


    public function testSomethingIsTrue()
    {
        $this->assertTrue(true);
    }

}
