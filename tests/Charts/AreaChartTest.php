<?php namespace Khill\Lavacharts\Tests\Charts;

use \Khill\Lavacharts\Charts\AreaChart;

class AreaChartTest extends ChartTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->AreaChart = new AreaChart('MyTestChart', $this->mockDataTable);
    }

    public function testInstanceOfAreaChartWithType()
    {
        $this->assertInstanceOf('\Khill\Lavacharts\Charts\AreaChart', $this->AreaChart);
    }

    public function testTypeAreaChart()
    {
        $chart = $this->AreaChart;

        $this->assertEquals('AreaChart', $chart::TYPE);
    }

    public function testLabelAssignedViaConstructor()
    {
        $this->assertEquals('MyTestChart', $this->AreaChart->label);
    }

    public function testAreaOpacity()
    {
        $this->AreaChart->areaOpacity(0.6);

        $this->assertEquals(0.6, $this->AreaChart->getOption('areaOpacity'));
    }

    /**
     * @dataProvider nonNumericProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAreaOpacityWithBadTypes($badTypes)
    {
        $this->AreaChart->areaOpacity($badTypes);
    }

    public function testAxisTitlesPositionValidValues()
    {
        $this->AreaChart->axisTitlesPosition('in');
        $this->assertEquals('in', $this->AreaChart->getOption('axisTitlesPosition'));

        $this->AreaChart->axisTitlesPosition('out');
        $this->assertEquals('out', $this->AreaChart->getOption('axisTitlesPosition'));

        $this->AreaChart->axisTitlesPosition('none');
        $this->assertEquals('none', $this->AreaChart->getOption('axisTitlesPosition'));
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAxisTitlesPositionWithBadValue()
    {
        $this->AreaChart->axisTitlesPosition('happymeal');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAxisTitlesPositionWithBadType($badTypes)
    {
        $this->AreaChart->axisTitlesPosition($badTypes);
    }

    public function testFocusTarget()
    {
        $this->AreaChart->focusTarget('datum');
        $this->assertEquals('datum', $this->AreaChart->getOption('focusTarget'));

        $this->AreaChart->focusTarget('category');
        $this->assertEquals('category', $this->AreaChart->getOption('focusTarget'));
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testFocusTargetWithBadType($badTypes)
    {
        $this->AreaChart->focusTarget($badTypes);
    }

    public function testHorizontalAxis()
    {
        $this->AreaChart->hAxis($this->getMockHorizontalAxis());

        $this->assertTrue(is_array($this->AreaChart->getOption('hAxis')));
    }

    public function testIsStacked()
    {
        $this->AreaChart->isStacked(true);

        $this->assertTrue($this->AreaChart->getOption('isStacked'));
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testIsStackedWithBadType($badTypes)
    {
        $this->AreaChart->isStacked($badTypes);
    }

    public function testInterpolateNulls()
    {
        $this->AreaChart->interpolateNulls(true);

        $this->assertTrue($this->AreaChart->getOption('interpolateNulls'));
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testInterpolateNullsWithBadType($badTypes)
    {
        $this->AreaChart->interpolateNulls($badTypes);
    }

    public function testLineWidth()
    {
        $this->AreaChart->lineWidth(22);

        $this->assertEquals(22, $this->AreaChart->getOption('lineWidth'));
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testLineWidthWithBadType($badTypes)
    {
        $this->AreaChart->lineWidth($badTypes);
    }

    public function testPointSize()
    {
        $this->AreaChart->pointSize(3);

        $this->assertEquals(3, $this->AreaChart->getOption('pointSize'));
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testPointSizeWithBadType($badTypes)
    {
        $this->AreaChart->pointSize($badTypes);
    }

    public function testVerticalAxis()
    {
        $this->AreaChart->vAxis($this->getMockVerticalAxis());

        $this->assertTrue(is_array($this->AreaChart->getOption('vAxis')));
    }
}
