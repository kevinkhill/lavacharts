<?php

namespace Khill\Lavacharts\Tests\Dashboards;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Dashboards\ChartWrapper;
use \Mockery as m;

class ChartWrapperTest extends ProvidersTestCase
{
    public $ChartWrapper;
    public $mockElementId;
    public $jsonOutput;

    public function setUp()
    {
        parent::setUp();

        $this->mockElementId = m::mock('\Khill\Lavacharts\Values\ElementId', ['TestId'])->makePartial();
        $this->jsonOutput = '{"chartType":"LineChart","containerId":"TestId","options":{"Option1":5,"Option2":true}}';
    }

    public function getMockLineChart()
    {
        return m::mock('\Khill\Lavacharts\Charts\LineChart')
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
    }

    /**
     * @covers \Khill\Lavacharts\Dashboards\Wrapper::getContainerId
     */
    public function testGetContainerId()
    {
        $areaChart = m::mock('\Khill\Lavacharts\Charts\AreaChart')->makePartial();

        $chartWrapper = new ChartWrapper($areaChart, $this->mockElementId);

        $this->assertEquals('TestId', $chartWrapper->getContainerId());
    }

    public function testGetChart()
    {
        $areaChart = m::mock('\Khill\Lavacharts\Charts\AreaChart')->makePartial();

        $chartWrapper = new ChartWrapper($areaChart, $this->mockElementId);

        $this->assertInstanceOf('\Khill\Lavacharts\Charts\AreaChart', $chartWrapper->getChart());
    }

    public function testJsonSerialization()
    {
        $this->ChartWrapper = new ChartWrapper($this->getMockLineChart(), $this->mockElementId);

        $this->assertEquals($this->jsonOutput, json_encode($this->ChartWrapper));
    }

    public function testToJavascript()
    {
        $this->ChartWrapper = new ChartWrapper($this->getMockLineChart(), $this->mockElementId);

        $javascript = 'new google.visualization.ChartWrapper('.$this->jsonOutput.')';

        $this->assertEquals($javascript, $this->ChartWrapper->toJavascript());
    }
}
