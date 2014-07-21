<?php namespace Khill\Lavacharts\Tests\Charts;

use Khill\Lavacharts\Tests\DataProviders;
use Khill\Lavacharts\Charts\Chart;

class ChartTest extends DataProviders
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
