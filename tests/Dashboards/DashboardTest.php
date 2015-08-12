<?php

namespace Khill\Lavacharts\Tests\Dashboards;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Dashboards\Dashboard;
use \Mockery as m;

class DashboardTest extends ProvidersTestCase
{
    public $Dashboard;
    public $mockControlWrapper;
    public $mockChartWrapper;

    public function setUp()
    {
        parent::setUp();

        $mockLabel = m::mock('\Khill\Lavacharts\Values\Label', ['myDash'])->makePartial();

        $this->Dashboard = new Dashboard($mockLabel);
        $this->mockControlWrapper = m::mock('\Khill\Lavacharts\Dashboards\ControlWrapper');
        $this->mockChartWrapper   = m::mock('\Khill\Lavacharts\Dashboards\ChartWrapper');
    }

    public function testGetLabel()
    {
        $this->assertEquals('myDash', (string) $this->Dashboard->getLabel());
    }

    /**
     * @covers \Khill\Lavacharts\Dashboards\Dashboard::getBindings
     */
    public function testGetBindings()
    {
        $this->Dashboard->bind($this->mockControlWrapper, $this->mockChartWrapper);

        $bindings = $this->Dashboard->getBindings();

        $this->assertTrue(is_array($bindings));
    }

    /**
     * @depends testGetBindings
     * @covers \Khill\Lavacharts\Dashboards\Dashboard::getBindings
     * @covers \Khill\Lavacharts\Dashboards\Bindings\OneToOne
     * @covers \Khill\Lavacharts\Dashboards\Bindings\BindingFactory::create
     */
    public function testBindWithOneToOne()
    {
        $this->Dashboard->bind($this->mockControlWrapper, $this->mockChartWrapper);

        $bindings = $this->Dashboard->getBindings();

        $this->assertInstanceOf('\Khill\Lavacharts\Dashboards\Bindings\OneToOne', $bindings[0]);
    }

    /**
     * @depends testGetBindings
     * @covers \Khill\Lavacharts\Dashboards\Dashboard::getBindings
     * @covers \Khill\Lavacharts\Dashboards\Bindings\OneToMany
     * @covers \Khill\Lavacharts\Dashboards\Bindings\BindingFactory::create
     */
    public function testBindWithOneToMany()
    {
        $this->Dashboard->bind(
            $this->mockControlWrapper,
            [$this->mockChartWrapper, $this->mockChartWrapper]
        );

        $bindings = $this->Dashboard->getBindings();

        $this->assertInstanceOf('\Khill\Lavacharts\Dashboards\Bindings\OneToMany', $bindings[0]);
    }

    /**
     * @depends testGetBindings
     * @covers \Khill\Lavacharts\Dashboards\Dashboard::getBindings
     * @covers \Khill\Lavacharts\Dashboards\Bindings\ManyToOne
     * @covers \Khill\Lavacharts\Dashboards\Bindings\BindingFactory::create
     */
    public function testBindWithManyToOne()
    {
        $this->Dashboard->bind(
            [$this->mockControlWrapper, $this->mockControlWrapper],
            $this->mockChartWrapper
        );

        $bindings = $this->Dashboard->getBindings();

        $this->assertInstanceOf('\Khill\Lavacharts\Dashboards\Bindings\ManyToOne', $bindings[0]);
    }

    /**
     * @depends testGetBindings
     * @covers \Khill\Lavacharts\Dashboards\Dashboard::getBindings
     * @covers \Khill\Lavacharts\Dashboards\Bindings\ManyToMany
     * @covers \Khill\Lavacharts\Dashboards\Bindings\BindingFactory::create
     */
    public function testBindWithManyToMany()
    {
        $this->Dashboard->bind(
            [$this->mockControlWrapper, $this->mockControlWrapper],
            [$this->mockChartWrapper, $this->mockChartWrapper]
        );

        $bindings = $this->Dashboard->getBindings();

        $this->assertInstanceOf('\Khill\Lavacharts\Dashboards\Bindings\ManyToMany', $bindings[0]);
    }

    /**
     * @depends testGetBindings
     * @covers \Khill\Lavacharts\Dashboards\Bindings\Binding
     */
    public function testGettingComponentsFromBinding()
    {
        $this->Dashboard->bind($this->mockControlWrapper, $this->mockChartWrapper);

        $bindings = $this->Dashboard->getBindings();

        $this->assertInstanceOf('\Khill\Lavacharts\Dashboards\Bindings\OneToOne', $bindings[0]);
        $this->assertInstanceOf('\Khill\Lavacharts\Dashboards\ControlWrapper', $bindings[0]->getControlWrappers()[0]);
        $this->assertInstanceOf('\Khill\Lavacharts\Dashboards\ChartWrapper', $bindings[0]->getChartWrappers()[0]);
    }
    /**
     * @depends testGetBindings
     * @depends testBindWithOneToMany
     */
    public function testGetBoundChartsWithOneToMany()
    {
        $mockElementId = m::mock('\Khill\Lavacharts\Values\ElementId', ['TestId'])->makePartial();

        $mockLineChartWrapper = m::mock('\Khill\Lavacharts\Dashboards\ChartWrapper', [
            m::mock('\Khill\Lavacharts\Charts\LineChart')->makePartial(),
            $mockElementId
        ])->makePartial();
            //->shouldReceive('unwrap')
            //->once()->getMock();
            //->andReturn();

        $mockAreaChartWrapper = m::mock('\Khill\Lavacharts\Dashboards\ChartWrapper', [
            m::mock('\Khill\Lavacharts\Charts\AreaChart')->makePartial(),
            $mockElementId
        ])->makePartial();
            //->shouldReceive('unwrap')
            //->once()->getMock();
            //->andReturn();

        $this->Dashboard->bind(
            $this->mockControlWrapper,
            [$mockLineChartWrapper, $mockAreaChartWrapper]
        );

        $charts = $this->Dashboard->getBoundCharts();

        $this->assertTrue(is_array($charts));
        $this->assertInstanceOf('\Khill\Lavacharts\Charts\LineChart', $charts['LineChart']);
        $this->assertInstanceOf('\Khill\Lavacharts\Charts\AreaChart', $charts['AreaChart']);
    }
}
