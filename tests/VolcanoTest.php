<?php

namespace Khill\Lavacharts\Tests;

use Khill\Lavacharts\Volcano;

class VolcanoTest extends ProvidersTestCase
{
    /**
     * @var \Khill\Lavacharts\Volcano
     */
    public $Volcano;

    public function setUp()
    {
        parent::setUp();

        $this->volcano = new Volcano;

        $this->mockGoodLabel = \Mockery::mock('\Khill\Lavacharts\Values\Label', ['TestRenderable'])->makePartial();

        $this->mockBadLabel = \Mockery::mock('\Khill\Lavacharts\Values\Label', ['Pumpkins'])->makePartial();

        $this->mockLineChart = \Mockery::mock(new \Khill\Lavacharts\Charts\LineChart(
            $this->mockGoodLabel,
            $this->partialDataTable
        ))->shouldReceive('getLabel')->andReturn('TestRenderable')->getMock();

        $this->mockDashboard = \Mockery::mock('\Khill\Lavacharts\Dashboards\Dashboard', [
            $this->mockGoodLabel
        ])->shouldReceive('getLabel')->andReturn('TestRenderable')->getMock();
    }

    /**
     * @group chart
     */
    public function testStoreWithChart()
    {
        $this->volcano->store($this->mockLineChart);

        $volcanoCharts = $this->getPrivateProperty($this->volcano, 'charts');

        $chart = $volcanoCharts['LineChart'][$this->mockLabelStr];

        $this->assertInstanceOf(self::NS.'\Charts\Chart', $chart);
    }

    /**
     * @group dash
     */
    public function testStoreWithDashboard()
    {
        $chart = \Mockery::mock(new \Khill\Lavacharts\Charts\LineChart(
            $this->mockGoodLabel,
            $this->partialDataTable
        ));

        $this->assertEquals($this->Volcano->store($chart), $chart);
    }

    /**
     * @group chart
     * @depends testStoreWithChart
     */
    public function testCheckChart()
    {
        $chart = \Mockery::mock(new \Khill\Lavacharts\Charts\LineChart(
            $this->mockGoodLabel,
            $this->partialDataTable
        ));

        $this->Volcano->store($chart);

        $this->assertTrue($this->Volcano->checkChart('LineChart', $this->mockGoodLabel));

        $this->assertFalse($this->Volcano->checkChart('LaserChart', $this->mockGoodLabel));
        $this->assertFalse($this->Volcano->checkChart('LineChart', $this->mockBadLabel));
    }

    /**
     * @group chart
     * @depends testStoreWithChart
     * @depends testCheckChart
     */
    public function testGetChart()
    {
        $this->Volcano->store($this->mockLineChart);

        $this->assertInstanceOf('\Khill\Lavacharts\Charts\LineChart', $this->Volcano->get('LineChart', $this->mockGoodLabel));
    }

    /**
     * @group chart
     * @depends testStoreWithChart
     * @depends testCheckChart
     * @depends testGetChart
     * @expectedException \Khill\Lavacharts\Exceptions\ChartNotFound
     */
    public function testGetChartWithBadChartType()
    {
        $this->Volcano->store($this->mockLineChart);
        $this->Volcano->get('LaserChart', $this->mockGoodLabel);
    }

    /**
     * @group chart
     * @depends testStoreWithChart
     * @depends testCheckChart
     * @depends testGetChart
     * @expectedException \Khill\Lavacharts\Exceptions\ChartNotFound
     */
    public function testGetChartWithNonExistentLabel()
    {
        $this->Volcano->store($this->mockLineChart);
        $this->Volcano->get('LineChart', $this->mockBadLabel);
    }



    /**
     * @group dashboard
     */
    public function testStoreDashboard()
    {
        $this->assertEquals($this->Volcano->store($this->mockDashboard), $this->mockDashboard);
    }

    /**
     * @group dashboard
     * @depends testStoreDashboard
     */
    public function testCheckDashboard()
    {
        $this->Volcano->store($this->mockDashboard);

        $this->assertTrue($this->Volcano->checkDashboard($this->mockGoodLabel));
    }

    /**
     * @group dashboard
     * @depends testStoreWithDashboard
     * @depends testCheckDashboard
     */
    public function testGetDashboard()
    {
        $this->Volcano->store($this->mockDashboard);

        $dash = $this->Volcano->get('Dashboard', $this->mockGoodLabel);

        $this->assertInstanceOf('\Khill\Lavacharts\Dashboards\Dashboard', $dash);
    }

    /**
     * @group dashboard
     * @depends testStoreWithDashboard
     * @depends testCheckDashboard
     * @depends testGetDashboard
     * @expectedException \Khill\Lavacharts\Exceptions\DashboardNotFound
     */
    public function testGetDashboardWithBadLabel()
    {
        $this->Volcano->store($this->mockDashboard);

        $this->Volcano->get('Dashboard', $this->mockBadLabel);
    }

    /**
     * @group chart
     * @group dashboard
     * @depends testGetChart
     * @depends testGetDashboard
     */
    public function testGetAll()
    {
        $this->Volcano->store($this->mockLineChart);
        $this->Volcano->store($this->mockDashboard);

        foreach ($this->Volcano->getAll() as $renderable) {
            $this->assertInstanceOf('\Khill\Lavacharts\Support\Contracts\RenderableInterface', $renderable);
        }
    }
}
