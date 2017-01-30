<?php

namespace Khill\Lavacharts\Tests\Dashboards;

use Khill\Lavacharts\Dashboards\Bindings\BindingFactory;

/**
 * @property \Khill\Lavacharts\Dashboards\Bindings\BindingFactory factory
 */
class BindingFactoryTest extends DashboardsTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->factory = new BindingFactory;
    }

    /**
     * @covers \Khill\Lavacharts\Dashboards\Bindings\OneToOne
     * @covers \Khill\Lavacharts\Dashboards\Bindings\BindingFactory::create
     */
    public function testBindWithOneToOne()
    {
        $binding = $this->factory->create($this->mockControlWrap, $this->mockChartWrap);

        $this->assertInstanceOf('\Khill\Lavacharts\Dashboards\Bindings\OneToOne', $binding);
    }

    /**
     * @covers \Khill\Lavacharts\Dashboards\Bindings\OneToMany
     * @covers \Khill\Lavacharts\Dashboards\Bindings\BindingFactory::create
     */
    public function testBindWithOneToMany()
    {
        $binding = $this->factory->create(
            $this->mockControlWrap,
            [$this->mockChartWrap, $this->mockChartWrap]
        );

        $this->assertInstanceOf('\Khill\Lavacharts\Dashboards\Bindings\OneToMany', $binding);
    }

    /**
     * @covers \Khill\Lavacharts\Dashboards\Bindings\ManyToOne
     * @covers \Khill\Lavacharts\Dashboards\Bindings\BindingFactory::create
     */
    public function testBindWithManyToOne()
    {
        $binding = $this->factory->create(
            [$this->mockControlWrap, $this->mockControlWrap],
            $this->mockChartWrap
        );

        $this->assertInstanceOf('\Khill\Lavacharts\Dashboards\Bindings\ManyToOne', $binding);
    }

    /**
     * @covers \Khill\Lavacharts\Dashboards\Bindings\ManyToMany
     * @covers \Khill\Lavacharts\Dashboards\Bindings\BindingFactory::create
     */
    public function testBindWithManyToMany()
    {
        $binding = $this->factory->create(
            [$this->mockControlWrap, $this->mockControlWrap],
            [$this->mockChartWrap, $this->mockChartWrap]
        );

        $this->assertInstanceOf('\Khill\Lavacharts\Dashboards\Bindings\ManyToMany', $binding);
    }
}
