<?php

namespace Khill\Lavacharts\Tests\Charts;

use \Khill\Lavacharts\Charts\DonutChart;

class DonutChartTest extends ChartTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->DonutChart = new DonutChart('MyTestChart', $this->partialDataTable);
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
        $this->assertEquals('MyTestChart', $this->DonutChart->label);
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
