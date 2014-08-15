<?php namespace Lavacharts\Tests\Charts;

use \Lavacharts\Tests\ProvidersTestCase;
use \Lavacharts\Charts\AreaChart;
use \Mockery as m;

class AreaChartTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->ac = new AreaChart('MyTestChart');
    }

    public function testInstanceOfAreaChartWithType()
    {
    	$this->assertInstanceOf('\Lavacharts\Charts\AreaChart', $this->ac);
    }

    public function testTypeAreaChart()
    {
    	$this->assertEquals('AreaChart', $this->ac->type);
    }

    public function testLabelAssignedViaConstructor()
    {
    	$this->assertEquals('MyTestChart', $this->ac->label);
    }

    public function testAreaOpacity()
    {
        $this->ac->areaOpacity(0.6);

        $this->assertEquals(0.6, $this->ac->getOption('areaOpacity'));
    }

    /**
     * @dataProvider nonNumericProvider
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAreaOpacityWithBadTypes($badTypes)
    {
        $this->ac->areaOpacity($badTypes);
    }

    public function testAxisTitlesPositionValidValues()
    {
        $this->ac->axisTitlesPosition('in');
        $this->assertEquals('in', $this->ac->getOption('axisTitlesPosition'));

        $this->ac->axisTitlesPosition('out');
        $this->assertEquals('out', $this->ac->getOption('axisTitlesPosition'));

        $this->ac->axisTitlesPosition('none');
        $this->assertEquals('none', $this->ac->getOption('axisTitlesPosition'));
    }

    /**
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAxisTitlesPositionWithBadValue()
    {
        $this->ac->axisTitlesPosition('happymeal');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAxisTitlesPositionWithBadType($badTypes)
    {
        $this->ac->axisTitlesPosition($badTypes);
    }

    public function testHorizontalAxis()
    {
        $mockHorizontalAxis = m::mock('Lavacharts\Configs\HorizontalAxis');
        $mockHorizontalAxis->shouldReceive('toArray')->once()->andReturn(array(
            'hAxis' => array()
        ));

        $this->ac->hAxis($mockHorizontalAxis);

        $this->assertTrue(is_array($this->ac->getOption('hAxis')));
    }

    public function testIsStacked()
    {
        $this->ac->isStacked(true);

        $this->assertTrue($this->ac->getOption('isStacked'));
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testIsStackedWithBadType($badTypes)
    {
        $this->ac->isStacked($badTypes);
    }

    public function testInterpolateNulls()
    {
        $this->ac->interpolateNulls(true);

        $this->assertTrue($this->ac->getOption('interpolateNulls'));
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testInterpolateNullsWithBadType($badTypes)
    {
        $this->ac->interpolateNulls($badTypes);
    }

    public function testLineWidth()
    {
        $this->ac->lineWidth(22);

        $this->assertEquals(22, $this->ac->getOption('lineWidth'));
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testLineWidthWithBadType($badTypes)
    {
        $this->ac->lineWidth($badTypes);
    }

    public function testPointSize()
    {
        $this->ac->pointSize(3);

        $this->assertEquals(3, $this->ac->getOption('pointSize'));
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testPointSizeWithBadType($badTypes)
    {
        $this->ac->pointSize($badTypes);
    }

    public function testVerticalAxis()
    {
        $mockVerticalAxis = m::mock('Lavacharts\Configs\VerticalAxis');
        $mockVerticalAxis->shouldReceive('toArray')->once()->andReturn(array(
            'vAxis' => array()
        ));

        $this->ac->vAxis($mockVerticalAxis);

        $this->assertTrue(is_array($this->ac->getOption('vAxis')));
    }
}
