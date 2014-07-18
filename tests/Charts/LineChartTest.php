<?php namespace Khill\Lavacharts\Tests\Charts;

use Khill\Lavacharts\Charts\LineChart;

class LineChartTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->linechart = new LineChart('test');
    }

    public function testInstanceOfLineChartWithType()
    {
    	$this->assertInstanceOf('Khill\Lavacharts\Charts\LineChart', $this->linechart);
    }

    public function testTypeLineChart()
    {
    	$this->assertEquals('LineChart', $this->linechart->type);
    }

    public function testLabelAssignedViaConstructor()
    {
    	$this->assertEquals('test', $this->linechart->label);
    }
}
