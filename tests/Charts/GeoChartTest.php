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

    public function testColorAxis()
    {
        $mockColorAxis = m::mock('Khill\Lavacharts\Configs\ColorAxis');
        $mockColorAxis->shouldReceive('toArray')->once()->andReturn(array(
            'ColorAxis' => array()
        ));

        $this->gc->colorAxis($mockColorAxis);

        $this->assertTrue(is_array($this->gc->options['ColorAxis']));
    }

    public function testDatalessRegionColorWithValidValue()
    {
        $this->gc->datalessRegionColor('#F6B0C3');
        $this->assertEquals('#F6B0C3', $this->gc->options['datalessRegionColor']);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDatalessRegionColorWithBadType($badTypes)
    {
        $this->gc->datalessRegionColor($badTypes);
    }

    public function testDisplayModeValidValues()
    {
        $this->gc->displayMode('auto');
        $this->assertEquals('auto', $this->gc->options['displayMode']);

        $this->gc->displayMode('regions');
        $this->assertEquals('regions', $this->gc->options['displayMode']);

        $this->gc->displayMode('markers');
        $this->assertEquals('markers', $this->gc->options['displayMode']);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDisplayModeWithBadValue()
    {
        $this->gc->displayMode('breakfast scramble');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDisplayModeWithBadType($badTypes)
    {
        $this->gc->displayMode($badTypes);
    }

    public function testEnableRegionInteractivityWithValidValues()
    {
        $this->gc->enableRegionInteractivity(true);
        $this->assertTrue($this->gc->options['enableRegionInteractivity']);

        $this->gc->enableRegionInteractivity(false);
        $this->assertFalse($this->gc->options['enableRegionInteractivity']);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testEnableRegionInteractivityWithBadType($badTypes)
    {
        $this->gc->enableRegionInteractivity($badTypes);
    }

    public function testKeepAspectRatioWithValidValues()
    {
        $this->gc->keepAspectRatio(true);
        $this->assertTrue($this->gc->options['keepAspectRatio']);

        $this->gc->keepAspectRatio(false);
        $this->assertFalse($this->gc->options['keepAspectRatio']);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testKeepAspectRatioWithBadType($badTypes)
    {
        $this->gc->enableRegionInteractivity($badTypes);
    }

    public function testRegionWithValidValue()
    {
        $this->gc->region('#F6B0C3');
        $this->assertEquals('#F6B0C3', $this->gc->options['region']);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testRegionWithBadType($badTypes)
    {
        $this->gc->region($badTypes);
    }

    public function testMagnifiyingGlass()
    {
        $mockMagnifiyingGlass = m::mock('Khill\Lavacharts\Configs\MagnifiyingGlass');
        $mockMagnifiyingGlass->shouldReceive('toArray')->once()->andReturn(array(
            'MagnifiyingGlass' => array()
        ));

        $this->gc->magnifyingGlass($mockMagnifiyingGlass);

        $this->assertTrue(is_array($this->gc->options['MagnifiyingGlass']));
    }

    public function testResolutionValidValues()
    {
        $this->gc->resolution('countries');
        $this->assertEquals('countries', $this->gc->options['resolution']);

        $this->gc->resolution('provinces');
        $this->assertEquals('provinces', $this->gc->options['resolution']);

        $this->gc->resolution('metros');
        $this->assertEquals('metros', $this->gc->options['resolution']);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testResolutionWithBadValue()
    {
        $this->gc->resolution('the borrowers');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testResolutionWithBadType($badTypes)
    {
        $this->gc->resolution($badTypes);
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
