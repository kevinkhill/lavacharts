<?php

namespace Khill\Lavacharts\Tests\Dashboards;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Dashboards\ChartWrapper;

class ChartWrapperTest extends ProvidersTestCase
{
    public $ChartWrapper;
    public $mockElementId;
    public $jsonOutput;

    public function setUp()
    {
        parent::setUp();

        $this->mockElementId = \Mockery::mock('\Khill\Lavacharts\Values\ElementId', ['TestId'])->makePartial();
        $this->jsonOutput = '{"chartType":"LineChart","containerId":"TestId","options":{"Option1":5,"Option2":true}}';
    }

    public function getMockLineChart()
    {
        return \Mockery::mock('\Khill\Lavacharts\Charts\LineChart')
            ->shouldReceive('getType')
            ->once()
            ->andReturn('LineChart')
            ->shouldReceive('jsonSerialize')
            ->once()
            ->andReturn([
                'Option1' => 5,
                'Option2' => true
            ])
            ->getMock();
    }

    /**
     * @covers \Khill\Lavacharts\Dashboards\Wrapper::getElementId
     */
    public function testGetContainerId()
    {
        $areaChart = \Mockery::mock('\Khill\Lavacharts\Charts\AreaChart')->makePartial();

        $chartWrapper = new ChartWrapper($areaChart, $this->mockElementId);

        $this->assertEquals('TestId', $chartWrapper->getElementId());
    }

    public function testGetChart()
    {
        $areaChart = \Mockery::mock('\Khill\Lavacharts\Charts\AreaChart')->makePartial();

        $chartWrapper = new ChartWrapper($areaChart, $this->mockElementId);

        $this->assertInstanceOf('\Khill\Lavacharts\Charts\AreaChart', $chartWrapper->unwrap());
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
