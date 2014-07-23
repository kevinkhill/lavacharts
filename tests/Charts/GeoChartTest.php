<?php namespace Khill\Lavacharts\Tests\Charts;

use Khill\Lavacharts\Tests\DataProviders;
use Khill\Lavacharts\Charts\GeoChart;
use Mockery as m;

class GeoChartTest extends DataProviders
{
    public function setUp()
    {
        parent::setUp();

        $this->gc = new GeoChart('MyTestChart');
    }

    public function testInstanceOfGeoChartWithType()
    {
    	$this->assertInstanceOf('Khill\Lavacharts\Charts\GeoChart', $this->gc);
    }

    public function testTypeGeoChart()
    {
    	$this->assertEquals('GeoChart', $this->gc->type);
    }

    public function testLabelAssignedViaConstructor()
    {
    	$this->assertEquals('MyTestChart', $this->gc->label);
    }

    public function testAxisTitlesPositionValidValues()
    {
        $this->gc->axisTitlesPosition('in');
        $this->assertEquals('in', $this->gc->options['axisTitlesPosition']);

        $this->gc->axisTitlesPosition('out');
        $this->assertEquals('out', $this->gc->options['axisTitlesPosition']);

        $this->gc->axisTitlesPosition('none');
        $this->assertEquals('none', $this->gc->options['axisTitlesPosition']);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAxisTitlesPositionWithBadValue()
    {
        $this->gc->axisTitlesPosition('happymeal');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAxisTitlesPositionWithBadType($badTypes)
    {
        $this->gc->axisTitlesPosition($badTypes);
    }

    public function testBarGroupWidthWithInt()
    {
        $this->gc->barGroupWidth(200);

        $this->assertEquals(200, $this->gc->options['bar']['groupWidth']);
    }

    public function testBarGroupWidthWithPercent()
    {
        $this->gc->barGroupWidth('33%');

        $this->assertEquals('33%', $this->gc->options['bar']['groupWidth']);
    }

    /**
     * @dataProvider nonIntOrPercentProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testBarGroupWidthWithBadTypes($badTypes)
    {
        $this->gc->barGroupWidth($badTypes);
    }

    public function testHorizontalAxis()
    {
        $mockHorizontalAxis = m::mock('Khill\Lavacharts\Configs\HorizontalAxis');
        $mockHorizontalAxis->shouldReceive('toArray')->once()->andReturn(array(
            'hAxis' => array()
        ));

        $this->gc->hAxis($mockHorizontalAxis);

        $this->assertTrue(is_array($this->gc->options['hAxis']));
    }

    public function testIsStacked()
    {
        $this->gc->isStacked(true);

        $this->assertTrue($this->gc->options['isStacked']);
    }

    /**
     * @dataProvider nonBooleanProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testIsStackedWithBadType($badTypes)
    {
        $this->gc->isStacked($badTypes);
    }

    public function testVerticalAxis()
    {
        $mockVerticalAxis = m::mock('Khill\Lavacharts\Configs\VerticalAxis');
        $mockVerticalAxis->shouldReceive('toArray')->once()->andReturn(array(
            'vAxis' => array()
        ));

        $this->gc->vAxis($mockVerticalAxis);

        $this->assertTrue(is_array($this->gc->options['vAxis']));
    }

    public function nonIntOrPercentProvider()
    {
        return array(
            array(3.2),
            array(true),
            array(false),
            array(array()),
            array(new \stdClass)
        );
    }
}
