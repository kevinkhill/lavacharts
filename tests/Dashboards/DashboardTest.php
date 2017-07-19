<?php

namespace Khill\Lavacharts\Tests\Dashboards;

use Khill\Lavacharts\Dashboards\Dashboard;

/**
 * @property \Khill\Lavacharts\Dashboards\Dashboard   dashboard
 */
class DashboardTest extends DashboardsTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->dashboard = new Dashboard(
            \Mockery::mock('\Khill\Lavacharts\Values\Label', ['myDash'])->makePartial(),
            $this->partialDataTable,
            \Mockery::mock('\Khill\Lavacharts\Values\ElementId', ['my-dash'])->makePartial()
        );
    }

    /**
     * @covers \Khill\Lavacharts\Dashboards\Bindings\BindingFactory::create
     * @expectedException \Khill\Lavacharts\Exceptions\BindingException
     */
    public function testBindingFactoryWithBadTypes()
    {
        $this->dashboard->bind(612345, 'tacos');
        $this->dashboard->bind(61.345, []);
        $this->dashboard->bind([], false);
    }
    /**
     * @covers \Khill\Lavacharts\Dashboards\Dashboard::bind
     * @covers \Khill\Lavacharts\Dashboards\Dashboard::getBindings
     */
    public function testGetBindings()
    {
        $this->dashboard->bind($this->mockControlWrap, $this->mockChartWrap);

        $bindings = $this->dashboard->getBindings();

        $this->assertTrue(is_array($bindings));
    }

    /**
     * @depends testGetBindings
     * @covers \Khill\Lavacharts\Dashboards\Dashboard::bind
     * @covers \Khill\Lavacharts\Dashboards\Dashboard::getBindings
     * @covers \Khill\Lavacharts\Dashboards\Bindings\OneToOne
     * @covers \Khill\Lavacharts\Dashboards\Bindings\BindingFactory::create
     */
    public function testBindWithOneToOne()
    {
        $this->dashboard->bind($this->mockControlWrap, $this->mockChartWrap);

        /** @var \Khill\Lavacharts\Dashboards\Bindings\Binding $binding */
        $binding = $this->dashboard->getBindings()[0];

        $this->assertInstanceOf('\Khill\Lavacharts\Dashboards\Bindings\OneToOne', $binding);
    }

    /**
     * @depends testGetBindings
     * @covers \Khill\Lavacharts\Dashboards\Dashboard::bind
     * @covers \Khill\Lavacharts\Dashboards\Dashboard::getBindings
     * @covers \Khill\Lavacharts\Dashboards\Bindings\OneToMany
     * @covers \Khill\Lavacharts\Dashboards\Bindings\BindingFactory::create
     */
    public function testBindWithOneToMany()
    {
        $this->dashboard->bind(
            $this->mockControlWrap,
            [$this->mockChartWrap, $this->mockChartWrap]
        );

        /** @var \Khill\Lavacharts\Dashboards\Bindings\Binding $binding */
        $binding = $this->dashboard->getBindings()[0];

        $this->assertInstanceOf('\Khill\Lavacharts\Dashboards\Bindings\OneToMany', $binding);
    }

    /**
     * @depends testGetBindings
     * @covers \Khill\Lavacharts\Dashboards\Dashboard::bind
     * @covers \Khill\Lavacharts\Dashboards\Dashboard::getBindings
     * @covers \Khill\Lavacharts\Dashboards\Bindings\ManyToOne
     * @covers \Khill\Lavacharts\Dashboards\Bindings\BindingFactory::create
     */
    public function testBindWithManyToOne()
    {
        $this->dashboard->bind(
            [$this->mockControlWrap, $this->mockControlWrap],
            $this->mockChartWrap
        );

        /** @var \Khill\Lavacharts\Dashboards\Bindings\Binding $binding */
        $binding = $this->dashboard->getBindings()[0];

        $this->assertInstanceOf('\Khill\Lavacharts\Dashboards\Bindings\ManyToOne', $binding);
    }

    /**
     * @depends testGetBindings
     * @covers \Khill\Lavacharts\Dashboards\Dashboard::bind
     * @covers \Khill\Lavacharts\Dashboards\Dashboard::getBindings
     * @covers \Khill\Lavacharts\Dashboards\Bindings\ManyToMany
     * @covers \Khill\Lavacharts\Dashboards\Bindings\BindingFactory::create
     */
    public function testBindWithManyToMany()
    {
        $this->dashboard->bind(
            [$this->mockControlWrap, $this->mockControlWrap],
            [$this->mockChartWrap, $this->mockChartWrap]
        );

        /** @var \Khill\Lavacharts\Dashboards\Bindings\Binding $binding */
        $binding = $this->dashboard->getBindings()[0];

        $this->assertInstanceOf('\Khill\Lavacharts\Dashboards\Bindings\ManyToMany', $binding);
    }

    /**
     * @depends testGetBindings
     * @covers \Khill\Lavacharts\Dashboards\Dashboard::bind
     * @covers \Khill\Lavacharts\Dashboards\Bindings\Binding
     */
    public function testGettingComponentsFromBinding()
    {
        $this->dashboard->bind($this->mockControlWrap, $this->mockChartWrap);

        /** @var \Khill\Lavacharts\Dashboards\Bindings\Binding $binding */
        $binding = $this->dashboard->getBindings()[0];

        $this->assertInstanceOf('\Khill\Lavacharts\Dashboards\Bindings\OneToOne', $binding);
        $this->assertInstanceOf('\Khill\Lavacharts\Dashboards\Wrappers\ControlWrapper',$binding->getControlWrappers()[0]);
        $this->assertInstanceOf('\Khill\Lavacharts\Dashboards\Wrappers\ChartWrapper', $binding->getChartWrappers()[0]);
    }
    /**
     * @depends testGetBindings
     * @depends testBindWithOneToMany
     * @covers \Khill\Lavacharts\Dashboards\Dashboard::getBoundCharts
     */
    public function testGetBoundChartsWithOneToMany()
    {
        $mockLineChartWrapper = \Mockery::mock('\Khill\Lavacharts\Dashboards\Wrappers\ChartWrapper', [
            \Mockery::mock('\Khill\Lavacharts\Charts\LineChart')->makePartial(),
            \Mockery::mock('\Khill\Lavacharts\Values\ElementId', ['line-chart'])->makePartial()
        ])->makePartial();
        //->shouldReceive('unwrap')
        //->once()->getMock();
        //->andReturn();

        $mockAreaChartWrapper = \Mockery::mock('\Khill\Lavacharts\Dashboards\Wrappers\ChartWrapper', [
            \Mockery::mock('\Khill\Lavacharts\Charts\AreaChart')->makePartial(),
            \Mockery::mock('\Khill\Lavacharts\Values\ElementId', ['area-chart'])->makePartial()
        ])->makePartial();
        //->shouldReceive('unwrap')
        //->once()->getMock();
        //->andReturn();

        $this->dashboard->bind(
            $this->mockControlWrap,
            [$mockLineChartWrapper, $mockAreaChartWrapper]
        );

        $charts = $this->dashboard->getBoundCharts();

        $this->assertTrue(is_array($charts));
        $this->assertInstanceOf('\Khill\Lavacharts\Charts\LineChart', $charts[0]);
        $this->assertInstanceOf('\Khill\Lavacharts\Charts\AreaChart', $charts[1]);
    }

    /**
     * @depends testGetBindings
     * @depends testBindWithOneToOne
     * @covers \Khill\Lavacharts\Dashboards\Dashboard::setBindings
     */
    public function testSetBindingsWithMultipleOneToOne()
    {
        $this->dashboard->setBindings([
            [$this->mockControlWrap, $this->mockChartWrap],
            [$this->mockControlWrap, $this->mockChartWrap]
        ]);

        $bindings = $this->dashboard->getBindings();

        $this->assertInstanceOf('\Khill\Lavacharts\Dashboards\Bindings\OneToOne', $bindings[0]);
        $this->assertInstanceOf('\Khill\Lavacharts\Dashboards\Bindings\OneToOne', $bindings[1]);
    }
}
