<?php

namespace Khill\Lavacharts\Tests\Charts;

use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\Charts\ComboChart;

class ComboChartTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $label = \Mockery::mock('\Khill\Lavacharts\Values\Label', ['MyTestChart'])->makePartial();

        $this->ComboChart = new ComboChart($label, $this->partialDataTable);
    }

    public function testInstanceOfLineChartWithType()
    {
        $this->assertInstanceOf('\Khill\Lavacharts\Charts\ComboChart', $this->ComboChart);
    }

    public function testTypeLineChart()
    {
        $chart = $this->ComboChart;

        $this->assertEquals('ComboChart', $chart::TYPE);
    }

    public function testLabelAssignedViaConstructor()
    {
        $this->assertEquals('MyTestChart', (string) $this->ComboChart->getLabel());
    }

    public function testSeriesType()
    {
        $this->ComboChart->seriesType('bars');

        $this->assertEquals('bars', $this->ComboChart->seriesType);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testSeriesTypeWithBadValue()
    {
        $this->ComboChart->seriesType('cake');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testSeriesTypeWithBadTypes($badTypes)
    {
        $this->ComboChart->seriesType($badTypes);
    }
}
