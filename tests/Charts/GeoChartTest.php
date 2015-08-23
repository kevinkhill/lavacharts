<?php

namespace Khill\Lavacharts\Tests\Charts;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Charts\GeoChart;
use \Mockery as m;

class GeoChartTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $label = m::mock('\Khill\Lavacharts\Values\Label', ['MyTestChart'])->makePartial();

        $this->GeoChart = new GeoChart($label, $this->partialDataTable);
    }

    public function testInstanceOfGeoChartWithType()
    {
        $this->assertInstanceOf('\Khill\Lavacharts\Charts\GeoChart', $this->GeoChart);
    }

    public function testTypeGeoChart()
    {
        $chart = $this->GeoChart;

        $this->assertEquals('GeoChart', $chart::TYPE);
    }

    public function testLabelAssignedViaConstructor()
    {
        $this->assertEquals('MyTestChart', (string) $this->GeoChart->getLabel());
    }

    public function testDatalessRegionColorWithValidValue()
    {
        $this->GeoChart->datalessRegionColor('#F6B0C3');
        $this->assertEquals('#F6B0C3', $this->GeoChart->datalessRegionColor);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDatalessRegionColorWithBadType($badTypes)
    {
        $this->GeoChart->datalessRegionColor($badTypes);
    }

    public function testDisplayModeValidValues()
    {
        $this->GeoChart->displayMode('auto');
        $this->assertEquals('auto', $this->GeoChart->displayMode);

        $this->GeoChart->displayMode('regions');
        $this->assertEquals('regions', $this->GeoChart->displayMode);

        $this->GeoChart->displayMode('markers');
        $this->assertEquals('markers', $this->GeoChart->displayMode);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDisplayModeWithBadValue()
    {
        $this->GeoChart->displayMode('breakfast scramble');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDisplayModeWithBadType($badTypes)
    {
        $this->GeoChart->displayMode($badTypes);
    }

    public function testEnableRegionInteractivityWithValidValues()
    {
        $this->GeoChart->enableRegionInteractivity(true);
        $this->assertTrue($this->GeoChart->enableRegionInteractivity);

        $this->GeoChart->enableRegionInteractivity(false);
        $this->assertFalse($this->GeoChart->enableRegionInteractivity);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testEnableRegionInteractivityWithBadType($badTypes)
    {
        $this->GeoChart->enableRegionInteractivity($badTypes);
    }

    public function testKeepAspectRatioWithValidValues()
    {
        $this->GeoChart->keepAspectRatio(true);
        $this->assertTrue($this->GeoChart->keepAspectRatio);

        $this->GeoChart->keepAspectRatio(false);
        $this->assertFalse($this->GeoChart->keepAspectRatio);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testKeepAspectRatioWithBadType($badTypes)
    {
        $this->GeoChart->keepAspectRatio($badTypes);
    }

    public function testmarkerOpacityWithValidIntValues()
    {
        $this->GeoChart->markerOpacity(0);
        $this->assertEquals(0, $this->GeoChart->markerOpacity);

        $this->GeoChart->markerOpacity(1);
        $this->assertEquals(1, $this->GeoChart->markerOpacity);
    }

    public function testmarkerOpacityWithValidFloatValues()
    {
        $this->GeoChart->markerOpacity(0.0);
        $this->assertEquals(0.0, $this->GeoChart->markerOpacity);

        $this->GeoChart->markerOpacity(0.5);
        $this->assertEquals(0.5, $this->GeoChart->markerOpacity);

        $this->GeoChart->markerOpacity(1.0);
        $this->assertEquals(1.0, $this->GeoChart->markerOpacity);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMarkerOpacityWithUnderLimit()
    {
        $this->GeoChart->markerOpacity(-1);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMarkerOpacityWithOverLimit()
    {
        $this->GeoChart->markerOpacity(1.1);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMarkerOpacityWithBadType($badTypes)
    {
        $this->GeoChart->markerOpacity($badTypes);
    }

    public function testRegionWithValidValue()
    {
        $this->GeoChart->region('#F6B0C3');
        $this->assertEquals('#F6B0C3', $this->GeoChart->region);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testRegionWithBadType($badTypes)
    {
        $this->GeoChart->region($badTypes);
    }

    public function testMagnifiyingGlass()
    {
        $this->GeoChart->magnifyingGlass([]);

        $this->assertInstanceOf('\Khill\Lavacharts\Configs\MagnifyingGlass', $this->GeoChart->magnifyingGlass);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMagnifiyingGlassWithBadTypes($badVals)
    {
        $this->GeoChart->magnifyingGlass($badVals);
    }

    public function testResolutionValidValues()
    {
        $this->GeoChart->resolution('countries');
        $this->assertEquals('countries', $this->GeoChart->resolution);

        $this->GeoChart->resolution('provinces');
        $this->assertEquals('provinces', $this->GeoChart->resolution);

        $this->GeoChart->resolution('metros');
        $this->assertEquals('metros', $this->GeoChart->resolution);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testResolutionWithBadValue()
    {
        $this->GeoChart->resolution('AntMan');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testResolutionWithBadType($badTypes)
    {
        $this->GeoChart->resolution($badTypes);
    }

    public function testSizeAxis()
    {
        $this->GeoChart->sizeAxis([]);

        $this->assertInstanceOf('\Khill\Lavacharts\Configs\SizeAxis', $this->GeoChart->sizeAxis);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testSizeAxisWithBadTypes($badVals)
    {
        $this->GeoChart->sizeAxis($badVals);
    }
}
