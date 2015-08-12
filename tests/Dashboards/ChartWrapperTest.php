<?php

namespace Khill\Lavacharts\Tests\Dashboards;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Dashboards\ChartWrapper;
use \Mockery as m;

class ChartWrapperTest extends ProvidersTestCase
{
    public function testJsonSerialization()
    {
        $mockElementId = m::mock('\Khill\Lavacharts\Values\ElementId', ['TestId'])->makePartial();
        $mockLineChart = m::mock('\Khill\Lavacharts\Charts\LineChart')
            ->shouldReceive('getType')
            ->once()
            ->andReturn('LineChart')
            ->shouldReceive('getOptions')
            ->once()
            ->andReturn([
                'Option1' => 5,
                'Option2' => true
            ])
            ->getMock();

        $chartWrapper = new ChartWrapper($mockLineChart, $mockElementId);

        $this->assertEquals('{"chartType":"LineChart","containerId":"TestId","options":{"Option1":5,"Option2":true}}', json_encode($chartWrapper));
    }
}
