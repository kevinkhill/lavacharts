<?php namespace Khill\Lavacharts\Tests\Charts;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Charts\GeoChart;
use \Mockery as m;

class GeoChartTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->gc = new GeoChart('MyTestChart');
    }

    public function testInstanceOfGeoChartWithType()
    {
        $this->assertInstanceOf('\Khill\Lavacharts\Charts\GeoChart', $this->gc);
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
            'colorAxis' => array()
        ));

        $this->gc->colorAxis($mockColorAxis);

        $this->assertTrue(is_array($this->gc->getOption('colorAxis')));
    }

    public function testDatalessRegionColorWithValidValue()
    {
        $this->gc->datalessRegionColor('#F6B0C3');
        $this->assertEquals('#F6B0C3', $this->gc->getOption('datalessRegionColor'));
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
        $this->assertEquals('auto', $this->gc->getOption('displayMode'));

        $this->gc->displayMode('regions');
        $this->assertEquals('regions', $this->gc->getOption('displayMode'));

        $this->gc->displayMode('markers');
        $this->assertEquals('markers', $this->gc->getOption('displayMode'));
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
        $this->assertTrue($this->gc->getOption('enableRegionInteractivity'));

        $this->gc->enableRegionInteractivity(false);
        $this->assertFalse($this->gc->getOption('enableRegionInteractivity'));
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
        $this->assertTrue($this->gc->getOption('keepAspectRatio'));

        $this->gc->keepAspectRatio(false);
        $this->assertFalse($this->gc->getOption('keepAspectRatio'));
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testKeepAspectRatioWithBadType($badTypes)
    {
        $this->gc->keepAspectRatio($badTypes);
    }

    public function testmarkerOpacityWithValidIntValues()
    {
        $this->gc->markerOpacity(0);
        $this->assertEquals(0, $this->gc->getOption('markerOpacity'));

        $this->gc->markerOpacity(1);
        $this->assertEquals(1, $this->gc->getOption('markerOpacity'));
    }

    public function testmarkerOpacityWithValidFloatValues()
    {
        $this->gc->markerOpacity(0.0);
        $this->assertEquals(0.0, $this->gc->getOption('markerOpacity'));

        $this->gc->markerOpacity(0.5);
        $this->assertEquals(0.5, $this->gc->getOption('markerOpacity'));

        $this->gc->markerOpacity(1.0);
        $this->assertEquals(1.0, $this->gc->getOption('markerOpacity'));
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMarkerOpacityWithUnderLimit()
    {
        $this->gc->markerOpacity(-1);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMarkerOpacityWithOverLimit()
    {
        $this->gc->markerOpacity(1.1);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMarkerOpacityWithBadType($badTypes)
    {
        $this->gc->markerOpacity($badTypes);
    }

    public function testRegionWithValidValue()
    {
        $this->gc->region('#F6B0C3');
        $this->assertEquals('#F6B0C3', $this->gc->getOption('region'));
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
        $mockMagnifyingGlass = m::mock('Khill\Lavacharts\Configs\MagnifyingGlass');
        $mockMagnifyingGlass->shouldReceive('toArray')->once()->andReturn(array(
            'magnifyingGlass' => array()
        ));

        $this->gc->magnifyingGlass($mockMagnifyingGlass);

        $this->assertTrue(is_array($this->gc->getOption('magnifyingGlass')));
    }

    public function testResolutionValidValues()
    {
        $this->gc->resolution('countries');
        $this->assertEquals('countries', $this->gc->getOption('resolution'));

        $this->gc->resolution('provinces');
        $this->assertEquals('provinces', $this->gc->getOption('resolution'));

        $this->gc->resolution('metros');
        $this->assertEquals('metros', $this->gc->getOption('resolution'));
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

    public function testSizeAxis()
    {
        $mockSizeAxis = m::mock('Khill\Lavacharts\Configs\SizeAxis');
        $mockSizeAxis->shouldReceive('toArray')->once()->andReturn(array(
            'sizeAxis' => array()
        ));

        $this->gc->sizeAxis($mockSizeAxis);

        $this->assertTrue(is_array($this->gc->getOption('sizeAxis')));
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
