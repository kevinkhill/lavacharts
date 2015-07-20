<?php

namespace Khill\Lavacharts\Tests\Charts;

use \Khill\Lavacharts\Charts\GeoChart;
use \Mockery as m;

class GeoChartTest extends ChartTestCase
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

    public function testColorAxis()
    {
        $this->GeoChart->colorAxis($this->getMockColorAxis());

        $this->assertTrue(is_array($this->GeoChart->getOption('colorAxis')));
    }

    public function testDatalessRegionColorWithValidValue()
    {
        $this->GeoChart->datalessRegionColor('#F6B0C3');
        $this->assertEquals('#F6B0C3', $this->GeoChart->getOption('datalessRegionColor'));
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
        $this->assertEquals('auto', $this->GeoChart->getOption('displayMode'));

        $this->GeoChart->displayMode('regions');
        $this->assertEquals('regions', $this->GeoChart->getOption('displayMode'));

        $this->GeoChart->displayMode('markers');
        $this->assertEquals('markers', $this->GeoChart->getOption('displayMode'));
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
        $this->assertTrue($this->GeoChart->getOption('enableRegionInteractivity'));

        $this->GeoChart->enableRegionInteractivity(false);
        $this->assertFalse($this->GeoChart->getOption('enableRegionInteractivity'));
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
        $this->assertTrue($this->GeoChart->getOption('keepAspectRatio'));

        $this->GeoChart->keepAspectRatio(false);
        $this->assertFalse($this->GeoChart->getOption('keepAspectRatio'));
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
        $this->assertEquals(0, $this->GeoChart->getOption('markerOpacity'));

        $this->GeoChart->markerOpacity(1);
        $this->assertEquals(1, $this->GeoChart->getOption('markerOpacity'));
    }

    public function testmarkerOpacityWithValidFloatValues()
    {
        $this->GeoChart->markerOpacity(0.0);
        $this->assertEquals(0.0, $this->GeoChart->getOption('markerOpacity'));

        $this->GeoChart->markerOpacity(0.5);
        $this->assertEquals(0.5, $this->GeoChart->getOption('markerOpacity'));

        $this->GeoChart->markerOpacity(1.0);
        $this->assertEquals(1.0, $this->GeoChart->getOption('markerOpacity'));
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
        $this->assertEquals('#F6B0C3', $this->GeoChart->getOption('region'));
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
        $mockMagnifyingGlass = m::mock('Khill\Lavacharts\Configs\MagnifyingGlass', function ($mock) {
            $mock->shouldReceive('toArray')->once()->andReturn([
                'magnifyingGlass' => []
            ]);
        });

        $this->GeoChart->magnifyingGlass($mockMagnifyingGlass);

        $this->assertTrue(is_array($this->GeoChart->getOption('magnifyingGlass')));
    }

    public function testResolutionValidValues()
    {
        $this->GeoChart->resolution('countries');
        $this->assertEquals('countries', $this->GeoChart->getOption('resolution'));

        $this->GeoChart->resolution('provinces');
        $this->assertEquals('provinces', $this->GeoChart->getOption('resolution'));

        $this->GeoChart->resolution('metros');
        $this->assertEquals('metros', $this->GeoChart->getOption('resolution'));
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
        $this->GeoChart->sizeAxis($this->getMockSizeAxis());

        $this->assertTrue(is_array($this->GeoChart->getOption('sizeAxis')));
    }
}
