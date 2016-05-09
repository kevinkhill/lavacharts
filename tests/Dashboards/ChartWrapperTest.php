<?php

namespace Khill\Lavacharts\Tests\Dashboards;

use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\Dashboards\Wrappers\ChartWrapper;

class ChartWrapperTest extends ProvidersTestCase
{
    public $mockElementId;
    public $jsonOutput;

    public function setUp()
    {
        parent::setUp();

        $this->mockElementId = $this->getMockElementId('TestLabel');

        $this->jsonOutput = '{"options":{"Option1":5,"Option2":true},"containerId":"TestLabel","chartType":"LineChart"}';
    }

    public function getMockLineChart()
    {
        return \Mockery::mock('\Khill\Lavacharts\Charts\LineChart')
            ->shouldReceive('setRenderable')
            ->once()
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
     * @covers \Khill\Lavacharts\Dashboards\Wrappers\Wrapper::getElementId
     */
    public function testGetElementId()
    {
        $areaChart = \Mockery::mock('\Khill\Lavacharts\Charts\AreaChart')->makePartial();

        $chartWrapper = new ChartWrapper($areaChart, $this->mockElementId);

        $this->assertInstanceOf('\Khill\Lavacharts\Values\ElementId', $chartWrapper->getElementId());
        $this->assertEquals('TestLabel', $chartWrapper->getElementIdStr());
    }

    public function testUnwrap()
    {
        $areaChart = \Mockery::mock('\Khill\Lavacharts\Charts\AreaChart')->makePartial();

        $chartWrapper = new ChartWrapper($areaChart, $this->mockElementId);

        $this->assertInstanceOf('\Khill\Lavacharts\Charts\AreaChart', $chartWrapper->unwrap());
    }

    public function testJsonSerialization()
    {
        $chart = \Mockery::mock('\Khill\Lavacharts\Charts\LineChart')
            ->shouldReceive('setRenderable')
            ->once()
            ->shouldReceive('getType')
            ->once()
            ->andReturn('LineChart')
            ->shouldReceive('getWrapType')
            ->once()
            ->andReturn('chartType')
            ->shouldReceive('jsonSerialize')
            ->once()
            ->andReturn([
                'Option1' => 5,
                'Option2' => true
            ])
            ->getMock();

        $chartWrapper = new ChartWrapper($chart, $this->mockElementId);

        $this->assertEquals($this->jsonOutput, json_encode($chartWrapper));
    }

    /**
     * @depends testJsonSerialize
     */
    public function testToJson()
    {
        $chart = \Mockery::mock('\Khill\Lavacharts\Charts\LineChart')
            ->shouldReceive('setRenderable')
            ->once()
            ->shouldReceive('getType')
            ->once()
            ->andReturn('LineChart')
            ->shouldReceive('getWrapType')
            ->once()
            ->andReturn('chartType')
            ->shouldReceive('jsonSerialize')
            ->once()
            ->andReturn([
                'Option1' => 5,
                'Option2' => true
            ])
            ->getMock();

        $chartWrapper = new ChartWrapper($chart, $this->mockElementId);

        $this->assertEquals($this->jsonOutput, $chartWrapper->toJson());
    }

    public function testGetJsClass()
    {
        $chartWrapper = new ChartWrapper($this->getMockLineChart(), $this->mockElementId);

        $javascript = 'google.visualization.ChartWrapper';

        $this->assertEquals($javascript, $chartWrapper->getJsClass());
    }

    /**
     * @depends testJsonSerialization
     */
    public function testGetJsConstructor()
    {

        $chart = \Mockery::mock('\Khill\Lavacharts\Charts\LineChart')
            ->shouldReceive('setRenderable')
            ->once()
            ->shouldReceive('getType')
            ->once()
            ->andReturn('LineChart')
            ->shouldReceive('getWrapType')
            ->once()
            ->andReturn('chartType')
            ->getMock();

        $chartWrapper = new ChartWrapper($chart, $this->mockElementId);

        $this->assertEquals(
            'new google.visualization.ChartWrapper('.$this->jsonOutput.')',
            $chartWrapper->getJsConstructor()
        );
    }
}
