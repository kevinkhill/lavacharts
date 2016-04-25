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

        $this->Volcano = new Volcano;

        $this->mockGoodLabel = \Mockery::mock('\Khill\Lavacharts\Values\Label', ['TestRenderable'])->makePartial();

        $this->nonBadLabel = \Mockery::mock('\Khill\Lavacharts\Values\Label', ['Pumpkins'])->makePartial();

        $this->mockLineChart = \Mockery::mock(new \Khill\Lavacharts\Charts\LineChart(
            $this->mockGoodLabel,
            $this->partialDataTable
        ))->shouldReceive('getLabel')->andReturn('TestRenderable')->getMock();

        $this->mockDashboard = \Mockery::mock('\Khill\Lavacharts\Dashboards\Dashboard', [
            $this->mockGoodLabel
        ])->shouldReceive('getLabel')->andReturn('TestDash')->getMock();
    }

    /**
     * @group chart
     */
    public function testStoreChart()
    {
        $chart = \Mockery::mock(new \Khill\Lavacharts\Charts\LineChart(
            $this->mockGoodLabel,
            $this->partialDataTable
        ));

        $this->assertEquals($this->Volcano->store($chart), $chart);
    }

    /**
     * @group chart
     * @depends testStoreChart
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
        $this->assertFalse($this->Volcano->checkChart('LineChart', $this->nonBadLabel));
    }

    /**
     * @group chart
     * @depends testStoreChart
     * @depends testCheckChart
     */
    public function testGetChart()
    {
        $this->Volcano->store($this->mockLineChart);

        $this->assertInstanceOf('\Khill\Lavacharts\Charts\LineChart', $this->Volcano->get('LineChart', $this->mockGoodLabel));
    }

    /**
     * @group chart
     * @depends testStoreChart
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
     * @depends testStoreChart
     * @depends testCheckChart
     * @depends testGetChart
     * @expectedException \Khill\Lavacharts\Exceptions\ChartNotFound
     */
    public function testGetChartWithNonExistentLabel()
    {
        $this->Volcano->store($this->mockLineChart);
        $this->Volcano->get('LineChart', $this->nonBadLabel);
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

        $this->assertFalse($this->Volcano->checkDashboard($this->nonBadLabel));
    }

    /**
     * @group dashboard
     * @depends testStoreDashboard
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
     * @depends testStoreDashboard
     * @depends testCheckDashboard
     * @depends testGetDashboard
     * @expectedException \Khill\Lavacharts\Exceptions\DashboardNotFound
     */
    public function testGetDashboardWithNonExistentLabel()
    {
        $this->Volcano->store($this->mockDashboard);

        $this->Volcano->get($this->nonBadLabel);
    }
}
