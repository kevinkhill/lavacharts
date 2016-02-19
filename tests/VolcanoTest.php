<?php

namespace Khill\Lavacharts\Tests;

use \Khill\Lavacharts\Volcano;

class VolcanoTest extends ProvidersTestCase
{
    public $volcano;

    public function setUp()
    {
        parent::setUp();

        $this->volcano = new Volcano;

        $this->badLabel     = $this->getMockLabel('non-existant');

        $this->mockLabelStr = 'tacos';
        $this->mockLabel    = $this->getMockLabel($this->mockLabelStr);

        $this->mockOptions = \Mockery::mock(self::NS.'\Configs\Options', [[]])->makePartial();

        $this->mockLineChart = \Mockery::mock(self::NS.'\Charts\LineChart', [
            $this->mockLabel,
            $this->partialDataTable,
            $this->mockOptions,
            $this->getMockElemId('my-div')
        ])->shouldReceive('getLabel')
          ->andReturn($this->mockLabelStr)
          ->shouldReceive('getType')
          ->andReturn('LineChart')
          ->getMock();

        $this->mockDashboard = \Mockery::mock(self::NS.'\Dashboards\Dashboard', [
            $this->mockLabel,
            [],
            $this->getMockElemId('my-div')
        ])->shouldReceive('getLabel')
          ->andReturn($this->mockLabelStr)
          ->getMock();

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
        $dash = $this->volcano->store($this->mockDashboard);

        $volcanoDashes = $this->getPrivateProperty($this->volcano, 'dashboards');

        $this->assertInstanceOf(self::NS.'\Dashboards\Dashboard', $volcanoDashes[$this->mockLabelStr]);
    }

    /**
     * @group chart
     * @depends testStoreWithChart
     */
    public function testCheckChart()
    {
        $this->volcano->store($this->mockLineChart);

        $this->assertTrue( $this->volcano->checkChart('LineChart', $this->mockLabel));
        $this->assertFalse($this->volcano->checkChart('HairChart', $this->mockLabel));
        $this->assertFalse($this->volcano->checkChart('LineChart', $this->badLabel));
    }

    /**
     * @group chart
     * @depends testStoreWithChart
     * @depends testCheckChart
     */
    public function testGetChart()
    {
        $this->volcano->store($this->mockLineChart);

        $volcanoChart = $this->volcano->get('LineChart', $this->mockLabel);

        $this->assertInstanceOf(self::NS.'\Charts\LineChart', $volcanoChart);
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
        $this->volcano->store($this->mockLineChart);
        $this->volcano->get('LaserChart', $this->mockLabel);
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
        $this->volcano->store($this->mockLineChart);
        $this->volcano->get('LineChart', $this->badLabel);
    }

    /**
     * @group dashboard
     * @depends testStoreWithDashboard
     */
    public function testCheckDashboard()
    {
        $this->volcano->store($this->mockDashboard);

        $this->assertFalse($this->volcano->checkDashboard($this->badLabel));
    }

    /**
     * @group dashboard
     * @depends testStoreWithDashboard
     * @depends testCheckDashboard
     */
    public function testGetDashboard()
    {
        $this->volcano->store($this->mockDashboard);

        $volcanoDash = $this->volcano->get('Dashboard', $this->mockLabel);

        $this->assertInstanceOf(self::NS.'\Dashboards\Dashboard', $volcanoDash);
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
        //$this->volcano->store($this->mockDashboard);

        $this->volcano->get('Dashboard', $this->badLabel);
    }
}
