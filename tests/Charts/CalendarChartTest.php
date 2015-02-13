<?php namespace Khill\Lavacharts\Tests\Charts;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Charts\CalendarChart;
use \Mockery as m;

class CalendarChartTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->cc = new CalendarChart('MyTestChart');
    }

    public function testInstanceOfLineChartWithType()
    {
    	$this->assertInstanceOf('\Khill\Lavacharts\Charts\CalendarChart', $this->cc);
    }

    public function testTypeLineChart()
    {
    	$this->assertEquals('CalendarChart', $this->cc->type);
    }

    public function testLabelAssignedViaConstructor()
    {
    	$this->assertEquals('MyTestChart', $this->cc->label);
    }

    public function testCellColor()
    {
        $mockStroke = m::mock('Khill\Lavacharts\Configs\Stroke');
        $mockStroke->shouldReceive('toArray')->once()->andReturn(array(
            'cellColor' => array()
        ));

        $this->cc->cellColor($mockStroke);

        $this->assertTrue(is_array($this->cc->getOption('cellColor')));
    }

    public function testCellSize()
    {
        $this->cc->cellSize(3);

        $this->assertEquals(3, $this->cc->getOption('cellSize'));
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testCellSizeWithBadType($badTypes)
    {
        $this->cc->cellSize($badTypes);
    }

    public function testDayOfWeekLabel()
    {
        $mockTextStyle = m::mock('Khill\Lavacharts\Configs\TextStyle');
        $mockTextStyle->shouldReceive('toArray')->once()->andReturn(array(
            'dayOfWeekLabel' => array()
        ));

        $this->cc->dayOfWeekLabel($mockTextStyle);

        $this->assertTrue(is_array($this->cc->getOption('dayOfWeekLabel')));
    }

}
