<?php

namespace Khill\Lavacharts\Tests\Charts;

use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\Charts\GeoChart;

class GeoChartTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $label = \Mockery::mock('\Khill\Lavacharts\Values\Label', ['MyTestChart'])->makePartial();

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
}
