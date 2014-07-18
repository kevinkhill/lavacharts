<?php namespace Khill\Lavacharts\Tests\Charts;

use Khill\Lavacharts\Charts\Chart;

class ChartTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->chart = new Chart('test'); 
    }

    public function testInstanceOfChart()
    {
    	$this->assertInstanceOf('Khill\Lavacharts\Charts\Chart', $this->chart);
    }

    public function testLabelAssignedViaConstructor()
    {
    	$this->assertEquals('test', $this->chart->label);
    }

}
