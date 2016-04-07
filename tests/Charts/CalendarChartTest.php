<?php

namespace Khill\Lavacharts\Tests\Charts;

use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\Charts\CalendarChart;

class CalendarChartTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $label = \Mockery::mock('\Khill\Lavacharts\Values\Label', ['MyTestChart'])->makePartial();

        $this->CalendarChart = new CalendarChart($label, $this->partialDataTable);
    }

    public function testInstanceOfCalendarChartWithType()
    {
        $this->assertInstanceOf('\Khill\Lavacharts\Charts\CalendarChart', $this->CalendarChart);
    }

    public function testTypeCalendarChart()
    {
        $chart = $this->CalendarChart;

        $this->assertEquals('CalendarChart', $chart::TYPE);
    }

    public function testLabelAssignedViaConstructor()
    {
        $this->assertEquals('MyTestChart', (string) $this->CalendarChart->getLabel());
    }
}
