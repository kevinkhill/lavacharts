<?php

namespace Khill\Lavacharts\Tests\Dashboards;

use Khill\Lavacharts\Dashboards\Bindings\BindingFactory;
use Khill\Lavacharts\Dashboards\Bindings\ManyToMany;
use Khill\Lavacharts\Dashboards\Bindings\ManyToOne;
use Khill\Lavacharts\Dashboards\Bindings\OneToMany;
use Khill\Lavacharts\Dashboards\Bindings\OneToOne;

class BindingFactoryTest extends DashboardsTestCase
{
    /**
     * @covers \Khill\Lavacharts\Dashboards\Bindings\OneToOne
     * @covers \Khill\Lavacharts\Dashboards\Bindings\BindingFactory::create
     */
    public function testBindWithOneToOne()
    {
        $binding = BindingFactory::create($this->mockControlWrap, $this->mockChartWrap);

        $this->assertInstanceOf(OneToOne::class, $binding);
    }

    /**
     * @covers \Khill\Lavacharts\Dashboards\Bindings\OneToMany
     * @covers \Khill\Lavacharts\Dashboards\Bindings\BindingFactory::create
     */
    public function testBindWithOneToMany()
    {
        $binding = BindingFactory::create(
            $this->mockControlWrap,
            [$this->mockChartWrap, $this->mockChartWrap]
        );

        $this->assertInstanceOf(OneToMany::class, $binding);
    }

    /**
     * @covers \Khill\Lavacharts\Dashboards\Bindings\ManyToOne
     * @covers \Khill\Lavacharts\Dashboards\Bindings\BindingFactory::create
     */
    public function testBindWithManyToOne()
    {
        $binding = BindingFactory::create(
            [$this->mockControlWrap, $this->mockControlWrap],
            $this->mockChartWrap
        );

        $this->assertInstanceOf(ManyToOne::class, $binding);
    }

    /**
     * @covers \Khill\Lavacharts\Dashboards\Bindings\ManyToMany
     * @covers \Khill\Lavacharts\Dashboards\Bindings\BindingFactory::create
     */
    public function testBindWithManyToMany()
    {
        $binding = BindingFactory::create(
            [$this->mockControlWrap, $this->mockControlWrap],
            [$this->mockChartWrap, $this->mockChartWrap]
        );

        $this->assertInstanceOf(ManyToMany::class, $binding);
    }
}
