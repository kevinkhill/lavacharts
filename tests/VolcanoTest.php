<?php

namespace Khill\Lavacharts\Tests;

use Khill\Lavacharts\Charts\Chart;
use Khill\Lavacharts\Charts\LineChart;
use Khill\Lavacharts\Dashboards\Dashboard;
use Khill\Lavacharts\DataTables\DataTable;
use Khill\Lavacharts\Support\Renderable;
use Khill\Lavacharts\Volcano;
use Mockery;

class VolcanoTest extends ProvidersTestCase
{
    /**
     * @var Volcano
     */
    public $volcano;

    public function setUp()
    {
        parent::setUp();

        $this->volcano = new Volcano;
    }

    public function testStoringCharts()
    {
        $chart = $this->volcano->store($this->createMockChart('TestChart'));

        $renderables = $this->inspect($this->volcano, 'renderables');

        $this->assertInstanceOf(Chart::class, $chart);
        $this->assertInstanceOf(Chart::class, $renderables['TestChart']);
    }

    public function testStoringDashboards()
    {
        $dash = $this->volcano->store($this->createMockDashboard('TestDash'));

        $renderables = $this->inspect($this->volcano, 'renderables');

        $this->assertInstanceOf(Dashboard::class, $dash);
        $this->assertInstanceOf(Dashboard::class, $renderables['TestDash']);
    }

    public function testGetWithChartAndDashboard()
    {
        $this->volcano->store($this->createMockChart('TestChart'));
        $this->volcano->store($this->createMockDashboard('TestDash'));

        $this->assertInstanceOf(LineChart::class, $this->volcano->get('TestChart'));
        $this->assertInstanceOf(Dashboard::class, $this->volcano->get('TestDash'));
    }

    public function testExistsWithChartAndDashboard()
    {
        $this->volcano->store($this->createMockChart('TestChart'));
        $this->volcano->store($this->createMockDashboard('TestDash'));

        $this->assertTrue($this->volcano->exists('TestChart'));
        $this->assertTrue($this->volcano->exists('TestDash'));

        $this->assertFalse($this->volcano->exists('NoChartHere'));
    }

    public function testFindWithChartAndDashboard()
    {
        $this->volcano->store($this->createMockChart('TestChart'));
        $this->volcano->store($this->createMockDashboard('TestDash'));

        $this->assertInstanceOf(LineChart::class, $this->volcano->find('TestChart'));
        $this->assertInstanceOf(Dashboard::class, $this->volcano->find('TestDash'));
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\RenderableNotFound
     */
    public function testFindFailingWithNonExistentLabel()
    {
        $this->volcano->find('NoChartHere');
    }

    public function testGetCharts()
    {
        for ($a = 0; $a < 5; $a++) {
            $label = 'chart' . $a;

            $this->volcano->store($this->createMockChart($label));
        }

        $charts = $this->volcano->getCharts();

        $this->assertEquals(5, count($charts));

        foreach ($charts as $chart) {
            $this->assertInstanceOf(Chart::class, $chart);
        }
    }

    public function testGetDashboards()
    {
        for ($a = 0; $a < 3; $a++) {
            $label = 'dash' . $a;

            $this->volcano->store($this->createMockDashboard($label));
        }

        $dashboards = $this->volcano->getDashboards();

        $this->assertEquals(3, count($dashboards));

        foreach ($dashboards as $dashboard) {
            $this->assertInstanceOf(Dashboard::class, $dashboard);
        }
    }

    public function testToArray()
    {
        $this->volcano->store($this->createMockChart('chart1'));
        $this->volcano->store($this->createMockChart('chart2'));
        $this->volcano->store($this->createMockChart('chart3'));
        $this->volcano->store($this->createMockDashboard('dash1'));
        $this->volcano->store($this->createMockDashboard('dash2'));

        $renderables = $this->volcano->toArray();

        $this->assertTrue(is_array($renderables));
        $this->assertEquals(5, count($renderables));

        foreach ($renderables as $renderable) {
            $this->assertInstanceOf(Renderable::class, $renderable);
        }
    }

    public function testCountableInterface()
    {
        $this->volcano->store($this->createMockChart('chart1'));
        $this->volcano->store($this->createMockChart('chart2'));
        $this->volcano->store($this->createMockChart('chart3'));

        $this->assertEquals(3, count($this->volcano));
    }

    public function testIteratorAggregateInterface()
    {
        $this->volcano->store($this->createMockChart('chart1'));
        $this->volcano->store($this->createMockChart('chart2'));
        $this->volcano->store($this->createMockChart('chart3'));

        foreach ($this->volcano as $renderable) {
            $this->assertInstanceOf(Renderable::class, $renderable);
        }
    }

    private function createMockChart($label)
    {
        return Mockery::mock(LineChart::class, [
            $label,
            Mockery::mock(DataTable::class)
        ])
        ->shouldReceive('getLabel')
        ->andReturn($label)
        ->shouldReceive('getType')
        ->zeroOrMoreTimes()
        ->andReturn('LineChart')
        ->getMock();
    }

    private function createMockDashboard($label)
    {
        return Mockery::mock(Dashboard::class, [
            $label,
            Mockery::mock(DataTable::class)
        ])
        ->shouldReceive('getLabel')
        ->andReturn($label)
        ->getMock();
    }
}
