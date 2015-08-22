<?php

namespace Khill\Lavacharts\Tests\Charts;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Charts\ColumnChart;
use \Mockery as m;

class ColumnChartTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $label = m::mock('\Khill\Lavacharts\Values\Label', ['MyTestChart'])->makePartial();

        $this->ColumnChart = new ColumnChart($label, $this->partialDataTable);
    }

    public function testInstanceOfColumnChartWithType()
    {
        $this->assertInstanceOf('\Khill\Lavacharts\Charts\ColumnChart', $this->ColumnChart);
    }

    public function testTypeColumnChart()
    {
        $chart = $this->ColumnChart;

        $this->assertEquals('ColumnChart', $chart::TYPE);
    }

    public function testLabelAssignedViaConstructor()
    {
        $this->assertEquals('MyTestChart', (string)$this->ColumnChart->getLabel());
    }
}
