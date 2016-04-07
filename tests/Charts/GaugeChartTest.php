<?php

namespace Khill\Lavacharts\Tests\Charts;

use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\Charts\GaugeChart;

class GaugeChartTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $label = \Mockery::mock('\Khill\Lavacharts\Values\Label', ['Temps'])->makePartial();

        $this->GaugeChart = new GaugeChart($label, $this->partialDataTable);
    }

    public function testInstanceOfGaugeChartWithType()
    {
        $this->assertInstanceOf('\Khill\Lavacharts\Charts\GaugeChart', $this->GaugeChart);
    }

    public function testTypeGaugeChart()
    {
        $this->assertEquals('GaugeChart', $this->GaugeChart->getType());
    }

    public function testLabelAssignedViaConstructor()
    {
        $this->assertEquals('Temps', (string) $this->GaugeChart->getLabel());
    }
}
