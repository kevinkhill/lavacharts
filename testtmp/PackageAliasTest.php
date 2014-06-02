<?php namespace Khill\Lavacharts\TestCase;

use Khill\Lavacharts\Configs;

class PackageAliasTest extends TestCase {

    public function testFacadeReturnsLavaChartObject()
    {
        $this->assertTrue( get_class(Lava) === 'Lavacharts' );
    }

}
