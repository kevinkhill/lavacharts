<?php

namespace Khill\Lavacharts\Tests\Charts;

use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\Charts\BarChart;

class BarChartTest extends ProvidersTestCase
{
    public $BarChart;

    public function setUp()
    {
        parent::setUp();

        $label = \Mockery::mock('\Khill\Lavacharts\Values\Label', ['MyTestChart'])->makePartial();

        $this->BarChart = new BarChart($label, $this->partialDataTable);
    }

    public function testTypeBarChart()
    {
        $this->assertEquals('BarChart', BarChart::TYPE);
        $this->assertEquals('BarChart', $this->BarChart->getType());
    }

    public function testLabelAssignedViaConstructor()
    {
        $this->assertEquals('MyTestChart', (string) $this->BarChart->getLabel());
    }
}

