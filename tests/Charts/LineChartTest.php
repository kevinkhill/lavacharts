<?php namespace Khill\Lavacharts\Tests\Charts;

use Khill\Lavacharts\Charts\LineChart;

class LineChartTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->lc = new LineChart('test');
    }

    public function testInstanceOfLineChartWithType()
    {
    	$this->assertInstanceOf('Khill\Lavacharts\Charts\LineChart', $this->lc);
    }

    public function testTypeLineChart()
    {
    	$this->assertEquals('LineChart', $this->lc->type);
    }

    public function testLabelAssignedViaConstructor()
    {
    	$this->assertEquals('test', $this->lc->label);
    }
}
