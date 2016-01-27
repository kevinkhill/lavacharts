<?php

namespace Khill\Lavacharts\Tests\Charts;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Charts\AreaChart;

class AreaChartTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $label = \Mockery::mock('\Khill\Lavacharts\Values\Label', ['MyTestChart'])->makePartial();

        $this->AreaChart = new AreaChart($label, $this->partialDataTable);
    }

    public function testInstanceOfAreaChartWithType()
    {
        $this->assertInstanceOf('\Khill\Lavacharts\Charts\AreaChart', $this->AreaChart);
    }

    public function testTypeAreaChart()
    {
        $chart = $this->AreaChart;

        $this->assertEquals('AreaChart', $chart::TYPE);
    }

    public function testLabelAssignedViaConstructor()
    {
        $this->assertEquals('MyTestChart', (string) $this->AreaChart->getLabel());
    }
}
