<?php

namespace Khill\Lavacharts\Tests;

use \Khill\Lavacharts\Volcano;

class VolcanoTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->volcano = new Volcano;

        $this->mockChartLabel = \Mockery::mock('\Khill\Lavacharts\Values\Label', ['TestChart'])->makePartial();

        $this->mockDashLabel = \Mockery::mock('\Khill\Lavacharts\Values\Label', ['TestDash'])->makePartial();

        $this->nonExistentLabel = \Mockery::mock('\Khill\Lavacharts\Values\Label', ['Pumpkins'])->makePartial();

        $this->mockLineChart = \Mockery::mock('\Khill\Lavacharts\Charts\LineChart', [
            $this->mockChartLabel,
            $this->partialDataTable
        ])->shouldReceive('getLabel')->andReturn('TestChart')->getMock();

        $this->mockDashboard = \Mockery::mock('\Khill\Lavacharts\Dashboards\Dashboard', [
            $this->mockDashLabel
        ])->shouldReceive('getLabel')->andReturn('TestDash')->getMock();
    }

    /**
     * @group chart
     */
    public function testStoreChart()
    {
        $this->assertTrue($this->volcano->storeChart($this->mockLineChart));
    }

    /**
     * @group chart
     * @depends testStoreChart
     */
    public function testCheckChart()
    {
        $this->volcano->storeChart($this->mockLineChart);

        $this->assertTrue($this->volcano->checkChart('LineChart', $this->mockChartLabel));

        $this->assertFalse($this->volcano->checkChart('LaserChart', $this->mockChartLabel));
        $this->assertFalse($this->volcano->checkChart('LineChart', $this->nonExistentLabel));
    }

    /**
     * @group chart
     * @depends testStoreChart
     * @depends testCheckChart
     */
    public function testGetChart()
    {
        $this->volcano->storeChart($this->mockLineChart);

        $this->assertInstanceOf('\Khill\Lavacharts\Charts\LineChart', $this->volcano->getChart('LineChart', $this->mockChartLabel));
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
        $this->volcano->storeChart($this->mockLineChart);
        $this->volcano->getChart('LaserChart', $this->mockChartLabel);
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
        $this->volcano->storeChart($this->mockLineChart);
        $this->volcano->getChart('LineChart', $this->nonExistentLabel);
    }


    /**
     * @group dashboard
     */
    public function testStoreDashboard()
    {
        $this->assertTrue($this->volcano->storeDashboard($this->mockDashboard));
    }

    /**
     * @group dashboard
     * @depends testStoreDashboard
     */
    public function testCheckDashboard()
    {
        $this->volcano->storeDashboard($this->mockDashboard);

        $this->assertFalse($this->volcano->checkDashboard($this->nonExistentLabel));
    }

    /**
     * @group dashboard
     * @depends testStoreDashboard
     * @dataProvider nonStringProvider
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testCheckDashboardWithBadTypes($badTypes)
    {
        $this->volcano->storeDashboard($this->mockDashboard);
        $this->assertFalse($this->volcano->checkDashboard($badTypes));
    }

    /**
     * @group dashboard
     * @depends testStoreDashboard
     * @depends testCheckDashboard
     */
    public function testGetDashboard()
    {
        $this->volcano->storeDashboard($this->mockDashboard);

        $this->assertInstanceOf('\Khill\Lavacharts\Dashboards\Dashboard', $this->volcano->getDashboard($this->mockDashLabel));
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
        $this->volcano->storeDashboard($this->mockDashboard);

        $this->volcano->getDashboard($this->nonExistentLabel);
    }
}
