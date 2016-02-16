<?php

namespace Khill\Lavacharts\Tests;

use \Khill\Lavacharts\Volcano;

class VolcanoTest extends ProvidersTestCase
{
    public $Volcano;

    public function setUp()
    {
        parent::setUp();

        $this->Volcano = new Volcano;

        $this->mockChartLabel = \Mockery::mock('\Khill\Lavacharts\Values\Label', ['TestChart'])->makePartial();

        $this->mockDashLabel = \Mockery::mock('\Khill\Lavacharts\Values\Label', ['TestDash'])->makePartial();

        $this->nonExistantLabel = \Mockery::mock('\Khill\Lavacharts\Values\Label', ['Pumpkins'])->makePartial();

        $this->mockElementId = \Mockery::mock('\Khill\Lavacharts\Values\ElementId', ['mockElemId'])->makePartial();

        $this->mockOptions = \Mockery::mock('\Khill\Lavacharts\Configs\Options', [[]])->makePartial();

        $this->mockLineChart = \Mockery::mock('\Khill\Lavacharts\Charts\LineChart', [
            $this->mockChartLabel,
            $this->partialDataTable,
            $this->mockOptions,
            $this->mockElementId
        ])->shouldReceive('getLabel')
          ->andReturn('TestChart')
          ->shouldReceive('getType')
          ->andReturn('LineChart')
          ->getMock();

        $this->mockDashboard = \Mockery::mock('\Khill\Lavacharts\Dashboards\Dashboard', [
            $this->mockDashLabel
        ])->shouldReceive('getLabel')->andReturn('TestDash')->getMock();
    }

    /**
     * @group chart
     */
    public function testStoreChart()
    {
        $this->assertTrue($this->Volcano->store($this->mockLineChart));
    }

    /**
     * @group chart
     * @depends testStoreChart
     */
    public function testCheckChart()
    {
        $this->Volcano->store($this->mockLineChart);

        $this->assertTrue($this->Volcano->checkChart('LineChart', $this->mockChartLabel));

        $this->assertFalse($this->Volcano->checkChart('LaserChart', $this->mockChartLabel));
        $this->assertFalse($this->Volcano->checkChart('LineChart', $this->nonExistantLabel));
    }

    /**
     * @group chart
     * @depends testStoreChart
     * @depends testCheckChart
     */
    public function testGetChart()
    {
        $this->Volcano->store($this->mockLineChart);

        $this->assertInstanceOf('\Khill\Lavacharts\Charts\LineChart', $this->Volcano->get('LineChart', $this->mockChartLabel));
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
        $this->Volcano->get('LaserChart', $this->mockChartLabel);
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
        $this->Volcano->get('LineChart', $this->nonExistantLabel);
    }


    /**
     * @group dashboard
     */
    public function testStoreDashboard()
    {
        $this->assertTrue($this->Volcano->store($this->mockDashboard));
    }

    /**
     * @group dashboard
     * @depends testStoreDashboard
     */
    public function testCheckDashboard()
    {
        $this->Volcano->store($this->mockDashboard);

        $this->assertFalse($this->Volcano->checkDashboard($this->nonExistantLabel));
    }

    /**
     * @group dashboard
     * @depends testStoreDashboard
     * @depends testCheckDashboard
     */
    public function testGetDashboard()
    {
        $this->Volcano->store($this->mockDashboard);

        $this->assertInstanceOf('\Khill\Lavacharts\Dashboards\Dashboard', $this->Volcano->get($this->mockDashLabel));
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

        $this->Volcano->get($this->nonExistantLabel);
    }
}
