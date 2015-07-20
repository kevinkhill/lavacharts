<?php

namespace Khill\Lavacharts\Tests\Charts;

use \Khill\Lavacharts\Charts\LineChart;
use \Mockery as m;

class LineChartTest extends ChartTestCase
{
    public function setUp()
    {
        parent::setUp();

        $label = m::mock('\Khill\Lavacharts\Values\Label', ['MyTestChart'])->makePartial();

        $this->LineChart = new LineChart($label, $this->partialDataTable);
    }

    public function testInstanceOfLineChartWithType()
    {
        $this->assertInstanceOf('\Khill\Lavacharts\Charts\LineChart', $this->LineChart);
    }

    public function testTypeLineChart()
    {
        $chart = $this->LineChart;

        $this->assertEquals('LineChart', $chart::TYPE);
    }

    public function testLabelAssignedViaConstructor()
    {
        $this->assertEquals('MyTestChart', (string) $this->LineChart->getLabel());
    }

    public function testAxisTitlesPositionWithValidValues()
    {
        $this->LineChart->axisTitlesPosition('in');
        $this->assertEquals('in', $this->LineChart->getOption('axisTitlesPosition'));

        $this->LineChart->axisTitlesPosition('out');
        $this->assertEquals('out', $this->LineChart->getOption('axisTitlesPosition'));

        $this->LineChart->axisTitlesPosition('none');
        $this->assertEquals('none', $this->LineChart->getOption('axisTitlesPosition'));
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAxisTitlesPositionWithBadValue()
    {
        $this->LineChart->axisTitlesPosition('socks');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAxisTitlesPositionWithBadType($badTypes)
    {
        $this->LineChart->axisTitlesPosition($badTypes);
    }

    public function testCurveTypeWithValidValues()
    {
        $this->LineChart->curveType('none');
        $this->assertEquals('none', $this->LineChart->getOption('curveType'));

        $this->LineChart->curveType('function');
        $this->assertEquals('function', $this->LineChart->getOption('curveType'));
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testCurveTypeWithBadValue()
    {
        $this->LineChart->curveType('rocks');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testCurveTypeWithBadType($badTypes)
    {
        $this->LineChart->curveType($badTypes);
    }

    public function testFocusTarget()
    {
        $this->LineChart->focusTarget('datum');
        $this->assertEquals('datum', $this->LineChart->getOption('focusTarget'));

        $this->LineChart->focusTarget('category');
        $this->assertEquals('category', $this->LineChart->getOption('focusTarget'));
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testFocusTargetWithBadType($badTypes)
    {
        $this->LineChart->focusTarget($badTypes);
    }

    public function testHorizontalAxis()
    {
        $this->LineChart->hAxis($this->getMockHorizontalAxis());

        $this->assertTrue(is_array($this->LineChart->getOption('hAxis')));
    }

    public function testInterpolateNulls()
    {
        $this->LineChart->interpolateNulls(true);

        $this->assertTrue($this->LineChart->getOption('interpolateNulls'));
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testInterpolateNullsWithBadType($badTypes)
    {
        $this->LineChart->interpolateNulls($badTypes);
    }

    public function testLineWidth()
    {
        $this->LineChart->lineWidth(22);

        $this->assertEquals(22, $this->LineChart->getOption('lineWidth'));
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testLineWidthWithBadType($badTypes)
    {
        $this->LineChart->lineWidth($badTypes);
    }

    public function testPointSize()
    {
        $this->LineChart->pointSize(3);

        $this->assertEquals(3, $this->LineChart->getOption('pointSize'));
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testPointSizeWithBadType($badTypes)
    {
        $this->LineChart->pointSize($badTypes);
    }

    public function testVerticalAxis()
    {
        $this->LineChart->vAxis($this->getMockVerticalAxis());

        $this->assertTrue(is_array($this->LineChart->getOption('vAxis')));
    }
}
