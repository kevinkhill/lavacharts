<?php

namespace Khill\Lavacharts\Tests\Charts;

use \Khill\Lavacharts\Charts\DonutChart;
use \Mockery as m;

class DonutChartTest extends ChartTestCase
{
    public function setUp()
    {
        parent::setUp();

        $label = m::mock('\Khill\Lavacharts\Values\Label', ['MyTestChart'])->makePartial();

        $this->DonutChart = new DonutChart($label, $this->partialDataTable);
    }

    public function testInstanceOfDonutChartWithType()
    {
        $this->assertInstanceOf('\Khill\Lavacharts\Charts\DonutChart', $this->DonutChart);
    }

    public function testTypeDonutChart()
    {
        $chart = $this->DonutChart;

        $this->assertEquals('DonutChart', $chart::TYPE);
    }

    public function testLabelAssignedViaConstructor()
    {
        $this->assertEquals('MyTestChart', (string) $this->DonutChart->getLabel());
    }

    public function testPieHole()
    {
        $this->DonutChart->pieHole(0.23);

        $this->assertEquals(0.23, $this->DonutChart->getOption('pieHole'));
    }

    /**
     * @dataProvider nonFloatProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testPieHoleWithBadType($badTypes)
    {
        $this->DonutChart->pieHole($badTypes);
    }
}
