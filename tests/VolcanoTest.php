<?php

namespace Khill\Lavacharts\Tests;

use \Mockery as m;
use \Khill\Lavacharts\Volcano;

class VolcanoTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->volcano = new Volcano;

        $this->mockLineChart = m::mock('\Khill\Lavacharts\Charts\LineChart', [
            'TestChart',
            $this->partialDataTable
        ]);

        $this->mockDashboard = m::mock('\Khill\Lavacharts\Dashboard\Dashboard', [
            'MyDash'
        ]);
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

        $this->assertTrue($this->volcano->checkChart('LineChart', 'TestChart'));

        $this->assertFalse($this->volcano->checkChart('LaserChart', 'TestChart'));
        $this->assertFalse($this->volcano->checkChart('LineChart', 'testing123chart'));
    }

    /**
     * @group chart
     * @depends testStoreChart
     * @depends testCheckChart
     */
    public function testGetChart()
    {
        $this->volcano->storeChart($this->mockLineChart);

        $this->assertInstanceOf('\Khill\Lavacharts\Charts\LineChart', $this->volcano->getChart('LineChart', 'TestChart'));
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
        $this->volcano->getChart('LaserChart', 'TestChart');
    }

    /**
     * @group chart
     * @depends testStoreChart
     * @depends testCheckChart
     * @depends testGetChart
     * @expectedException \Khill\Lavacharts\Exceptions\ChartNotFound
     */
    public function testGetChartWithNonExistantLabel()
    {
        $this->volcano->storeChart($this->mockLineChart);
        $this->volcano->getChart('LineChart', 'superduperchart');
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

        $this->assertTrue($this->volcano->checkDashboard('MyDash'));

        $this->assertFalse($this->volcano->checkDashboard('Buckets'));
    }

    /**
     * @group dashboard
     * @depends testStoreDashboard
     * @dataProvder nonStringProvider
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

        $this->assertInstanceOf('\Khill\Lavacharts\Dashboard\Dashboard', $this->volcano->getDashboard('MyDash'));
    }

    /**
     * @group dashboard
     * @depends testStoreDashboard
     * @depends testCheckDashboard
     * @depends testGetDashboard
     * @expectedException \Khill\Lavacharts\Exceptions\DashboardNotFound
     */
    public function testGetDashboardWithNonExistantLabel()
    {
        $this->volcano->storeDashboard($this->mockDashboard);

        $this->volcano->getDashboard('Buckets');
    }
}
