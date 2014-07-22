<?php namespace Khill\Lavacharts\Tests\Configs;

use Khill\Lavacharts\Configs\Legend;

class LegendTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->l = new Legend();

        $this->mockTextStyle = $this->getMock(
            'Khill\Lavacharts\Configs\TextStyle',
            array('__construct')
        );
    }

    public function testIfInstanceOfLegend()
    {
        $this->assertInstanceOf('Khill\Lavacharts\Configs\Legend', $this->l);
    }

}

