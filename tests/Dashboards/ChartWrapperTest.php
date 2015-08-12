<?php

namespace Khill\Lavacharts\Tests\Dashboards;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Dashboards\ChartWrapper;
use \Mockery as m;

class ChartWrapperTest extends ProvidersTestCase
{
    public $ChartWrapper;

    public function setUp()
    {
        parent::setUp();

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

        $this->ChartWrapper = new ChartWrapper($mockLineChart, $mockElementId);

    }

    public function testJsonSerialization()
    {
        $json = '{"chartType":"LineChart","containerId":"TestId","options":{"Option1":5,"Option2":true}}';

        $this->assertEquals($json, json_encode($this->ChartWrapper));
    }

    public function testToJavascript()
    {
        $json = '{"chartType":"LineChart","containerId":"TestId","options":{"Option1":5,"Option2":true}}';
        $javascript = "new google.visualization.ChartWrapper($json)";

        $this->assertEquals($javascript, $this->ChartWrapper->toJavascript());
    }
}
