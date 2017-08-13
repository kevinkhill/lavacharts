<?php

namespace Khill\Lavacharts\Tests\Dashboards;

use Khill\Lavacharts\Charts\AreaChart;
use Khill\Lavacharts\Dashboards\Wrappers\ChartWrapper;
use Khill\Lavacharts\Tests\ProvidersTestCase;
use Mockery;

/**
 * @property AreaChart mockAreaChart
 * @property string    jsonOutput
 */
class ChartWrapperTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->jsonOutput = '{"containerId":"test-div","options":[],"chartType":"AreaChart"}';

        $this->mockAreaChart = Mockery::mock(AreaChart::class);
        $this->mockAreaChart->shouldReceive(['getType' => 'AreaChart']);
    }

    /**
     * @covers \Khill\Lavacharts\Dashboards\Wrappers\Wrapper::getElementId
     */
    public function testGetElementId()
    {
        $chartWrapper = new ChartWrapper($this->mockAreaChart, 'test-div');

        $this->assertEquals('test-div', $chartWrapper->getElementId());
    }

    /**
     * @covers \Khill\Lavacharts\Dashboards\Wrappers\Wrapper::unwrap
     */
    public function testUnwrap()
    {
        $chartWrapper = new ChartWrapper($this->mockAreaChart, 'test-div');

        $this->assertInstanceOf(AreaChart::class, $chartWrapper->unwrap());
    }

    /**
     * @covers \Khill\Lavacharts\Dashboards\Wrappers\Wrapper::getJsClass
     */
    public function testGetJsClass()
    {
        $chartWrapper = new ChartWrapper($this->mockAreaChart, 'test-div');

        $javascript = 'google.visualization.ChartWrapper';

        $this->assertEquals($javascript, $chartWrapper->getJsClass());
    }

    public function testJsonSerialize()
    {
        $chartWrapper = new ChartWrapper($this->mockAreaChart, 'test-div');

        $this->assertJsonStringEqualsJsonString(
            $this->jsonOutput,
            json_encode($chartWrapper)
        );
    }

    public function testToJson()
    {
        $chartWrapper = new ChartWrapper($this->mockAreaChart, 'test-div');

        $this->assertJsonStringEqualsJsonString(
            $this->jsonOutput,
            $chartWrapper->toJson()
        );
    }
}
