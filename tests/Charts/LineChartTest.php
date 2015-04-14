<?php namespace Khill\Lavacharts\Tests\Charts;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Charts\LineChart;
use \Mockery as m;

class LineChartTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->lc = new LineChart('MyTestChart');
    }

    public function testInstanceOfLineChartWithType()
    {
        $this->assertInstanceOf('\Khill\Lavacharts\Charts\LineChart', $this->lc);
    }

    public function testTypeLineChart()
    {
        $this->assertEquals('LineChart', $this->lc->type);
    }

    public function testLabelAssignedViaConstructor()
    {
        $this->assertEquals('MyTestChart', $this->lc->label);
    }

    public function testAxisTitlesPositionWithValidValues()
    {
        $this->lc->axisTitlesPosition('in');
        $this->assertEquals('in', $this->lc->getOption('axisTitlesPosition'));

        $this->lc->axisTitlesPosition('out');
        $this->assertEquals('out', $this->lc->getOption('axisTitlesPosition'));

        $this->lc->axisTitlesPosition('none');
        $this->assertEquals('none', $this->lc->getOption('axisTitlesPosition'));
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAxisTitlesPositionWithBadValue()
    {
        $this->lc->axisTitlesPosition('socks');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAxisTitlesPositionWithBadType($badTypes)
    {
        $this->lc->axisTitlesPosition($badTypes);
    }

    public function testCurveTypeWithValidValues()
    {
        $this->lc->curveType('none');
        $this->assertEquals('none', $this->lc->getOption('curveType'));

        $this->lc->curveType('function');
        $this->assertEquals('function', $this->lc->getOption('curveType'));
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testCurveTypeWithBadValue()
    {
        $this->lc->curveType('rocks');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testCurveTypeWithBadType($badTypes)
    {
        $this->lc->curveType($badTypes);
    }

    public function testFocusTarget()
    {
        $this->lc->focusTarget('datum');
        $this->assertEquals('datum', $this->lc->getOption('focusTarget'));

        $this->lc->focusTarget('category');
        $this->assertEquals('category', $this->lc->getOption('focusTarget'));
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testFocusTargetWithBadType($badTypes)
    {
        $this->lc->focusTarget($badTypes);
    }

    public function testHorizontalAxis()
    {
        $mockHorizontalAxis = m::mock('Khill\Lavacharts\Configs\HorizontalAxis');
        $mockHorizontalAxis->shouldReceive('toArray')->once()->andReturn(array(
            'hAxis' => array()
        ));

        $this->lc->hAxis($mockHorizontalAxis);

        $this->assertTrue(is_array($this->lc->getOption('hAxis')));
    }

    public function testInterpolateNulls()
    {
        $this->lc->interpolateNulls(true);

        $this->assertTrue($this->lc->getOption('interpolateNulls'));
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testInterpolateNullsWithBadType($badTypes)
    {
        $this->lc->interpolateNulls($badTypes);
    }

    public function testLineWidth()
    {
        $this->lc->lineWidth(22);

        $this->assertEquals(22, $this->lc->getOption('lineWidth'));
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testLineWidthWithBadType($badTypes)
    {
        $this->lc->lineWidth($badTypes);
    }

    public function testPointSize()
    {
        $this->lc->pointSize(3);

        $this->assertEquals(3, $this->lc->getOption('pointSize'));
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testPointSizeWithBadType($badTypes)
    {
        $this->lc->pointSize($badTypes);
    }

    public function testVerticalAxis()
    {
        $mockVerticalAxis = m::mock('Khill\Lavacharts\Configs\VerticalAxis');
        $mockVerticalAxis->shouldReceive('toArray')->once()->andReturn(array(
            'vAxis' => array()
        ));

        $this->lc->vAxis($mockVerticalAxis);

        $this->assertTrue(is_array($this->lc->getOption('vAxis')));
    }
}
