<?php

namespace Khill\Lavacharts\Tests\Charts;

use \Khill\Lavacharts\Charts\ColumnChart;

class ColumnChartTest extends ChartTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->ColumnChart = new ColumnChart('MyTestChart', $this->partialDataTable);
    }

    public function testInstanceOfColumnChartWithType()
    {
        $this->assertInstanceOf('\Khill\Lavacharts\Charts\ColumnChart', $this->ColumnChart);
    }

    public function testTypeColumnChart()
    {
        $chart = $this->ColumnChart;

        $this->assertEquals('ColumnChart', $chart::TYPE);
    }

    public function testLabelAssignedViaConstructor()
    {
        $this->assertEquals('MyTestChart', $this->ColumnChart->label);
    }

    public function testAxisTitlesPositionValidValues()
    {
        $this->ColumnChart->axisTitlesPosition('in');
        $this->assertEquals('in', $this->ColumnChart->getOption('axisTitlesPosition'));

        $this->ColumnChart->axisTitlesPosition('out');
        $this->assertEquals('out', $this->ColumnChart->getOption('axisTitlesPosition'));

        $this->ColumnChart->axisTitlesPosition('none');
        $this->assertEquals('none', $this->ColumnChart->getOption('axisTitlesPosition'));
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAxisTitlesPositionWithBadValue()
    {
        $this->ColumnChart->axisTitlesPosition('happymeal');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAxisTitlesPositionWithBadType($badTypes)
    {
        $this->ColumnChart->axisTitlesPosition($badTypes);
    }

    public function testBarGroupWidthWithInt()
    {
        $this->ColumnChart->barGroupWidth(200);

        $bar = $this->ColumnChart->getOption('barGroupWidth');

        $this->assertEquals(200, $bar['groupWidth']);
    }

    public function testBarGroupWidthWithPercent()
    {
        $this->ColumnChart->barGroupWidth('33%');

        $bar = $this->ColumnChart->getOption('barGroupWidth');

        $this->assertEquals('33%', $bar['groupWidth']);
    }

    /**
     * @dataProvider nonIntOrPercentProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testBarGroupWidthWithBadTypes($badTypes)
    {
        $this->ColumnChart->barGroupWidth($badTypes);
    }

    public function testHorizontalAxis()
    {
        $this->ColumnChart->hAxis($this->getMockHorizontalAxis());

        $this->assertTrue(is_array($this->ColumnChart->getOption('hAxis')));
    }

    public function testIsStacked()
    {
        $this->ColumnChart->isStacked(true);

        $this->assertTrue($this->ColumnChart->getOption('isStacked'));
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testIsStackedWithBadType($badTypes)
    {
        $this->ColumnChart->isStacked($badTypes);
    }

    public function testVerticalAxis()
    {
        $this->ColumnChart->vAxis($this->getMockVerticalAxis());

        $this->assertTrue(is_array($this->ColumnChart->getOption('vAxis')));
    }
}
