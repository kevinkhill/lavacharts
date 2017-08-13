<?php

namespace Khill\Lavacharts\Tests\Dashboards;

use Khill\Lavacharts\Charts\AreaChart;
use Khill\Lavacharts\Charts\LineChart;
use Khill\Lavacharts\Dashboards\Bindings\Binding;
use Khill\Lavacharts\Dashboards\Bindings\ManyToMany;
use Khill\Lavacharts\Dashboards\Bindings\ManyToOne;
use Khill\Lavacharts\Dashboards\Bindings\OneToMany;
use Khill\Lavacharts\Dashboards\Bindings\OneToOne;
use Khill\Lavacharts\Dashboards\Dashboard;
use Khill\Lavacharts\Dashboards\Wrappers\ChartWrapper;
use Khill\Lavacharts\Dashboards\Wrappers\ControlWrapper;
use Khill\Lavacharts\DataTables\DataTable;
use Khill\Lavacharts\Support\Options;
use Mockery;

/**
 * @property Dashboard dashboard
 */
class DashboardTest extends DashboardsTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->dashboard = new Dashboard(
            'myDash',
            Mockery::mock(DataTable::class),
            ['elementId' => 'my-dash']
        );
    }

    public function testGetLabel()
    {
        $this->assertEquals('myDash', $this->dashboard->getLabel());
    }

    public function testGetElementId()
    {
        $this->assertEquals('my-dash', $this->dashboard->getElementId());
    }

    public function testGetOptions()
    {
        $this->assertInstanceOf(Options::class, $this->dashboard->getOptions());
    }

    public function testGetBindings()
    {
        $this->dashboard->bind($this->mockControlWrap, $this->mockChartWrap);
        $this->dashboard->bind($this->mockControlWrap, $this->mockChartWrap);

        $bindings = $this->dashboard->getBindings();

        $this->assertTrue(is_array($bindings));
        $this->assertCount(2, $bindings);
    }

    public function testBindWithOneToOne()
    {
        $this->dashboard->bind($this->mockControlWrap, $this->mockChartWrap);

        /** @var Binding $binding */
        $binding = $this->dashboard->getBindings()[0];

        $this->assertInstanceOf(OneToOne::class, $binding);
    }

    public function testBindWithOneToMany()
    {
        $this->dashboard->bind(
            $this->mockControlWrap,
            [$this->mockChartWrap, $this->mockChartWrap]
        );

        /** @var Binding $binding */
        $binding = $this->dashboard->getBindings()[0];

        $this->assertInstanceOf(OneToMany::class, $binding);
    }

    public function testBindWithManyToOne()
    {
        $this->dashboard->bind(
            [$this->mockControlWrap, $this->mockControlWrap],
            $this->mockChartWrap
        );

        /** @var Binding $binding */
        $binding = $this->dashboard->getBindings()[0];

        $this->assertInstanceOf(ManyToOne::class, $binding);
    }

    public function testBindWithManyToMany()
    {
        $this->dashboard->bind(
            [$this->mockControlWrap, $this->mockControlWrap],
            [$this->mockChartWrap, $this->mockChartWrap]
        );

        /** @var Binding $binding */
        $binding = $this->dashboard->getBindings()[0];

        $this->assertInstanceOf(ManyToMany::class, $binding);
    }

    public function testGettingComponentsFromBinding()
    {
        $this->dashboard->bind($this->mockControlWrap, $this->mockChartWrap);

        /** @var Binding $binding */
        $binding = $this->dashboard->getBindings()[0];

        $this->assertInstanceOf(OneToOne::class, $binding);
        $this->assertInstanceOf(ControlWrapper::class, $binding->getControlWrapper(0));
        $this->assertInstanceOf(ChartWrapper::class, $binding->getChartWrapper(0));
    }

    public function testGetPackagesWithOneToMany()
    {
        $mockLineChart = Mockery::mock(LineChart::class);
        $mockLineChart->shouldReceive(['getJsPackage' => 'linechart']);

        $mockAreaChart = Mockery::mock(AreaChart::class);
        $mockAreaChart->shouldReceive(['getJsPackage' => 'areachart']);

        $mockLineChartWrapper = Mockery::mock(ChartWrapper::class);
        $mockLineChartWrapper->shouldReceive(['unwrap' => $mockLineChart]);

        $mockAreaChartWrapper = Mockery::mock(ChartWrapper::class);
        $mockAreaChartWrapper->shouldReceive(['unwrap' => $mockAreaChart]);

        $this->dashboard->bind(
            $this->mockControlWrap,
            [$mockLineChartWrapper, $mockAreaChartWrapper]
        );

        $packages = $this->dashboard->getPackages();

        $this->assertTrue(is_array($packages));
        $this->assertContains('controls', $packages);
        $this->assertContains('linechart', $packages);
        $this->assertContains('areachart', $packages);
    }

    public function testSetBindingsWithMultipleOneToOne()
    {
        $this->dashboard->setBindings([
            [$this->mockControlWrap, $this->mockChartWrap],
            [$this->mockControlWrap, $this->mockChartWrap]
        ]);

        $bindings = $this->dashboard->getBindings();

        $this->assertInstanceOf(OneToOne::class, $bindings[0]);
        $this->assertInstanceOf(OneToOne::class, $bindings[1]);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\BindingException
     */
    public function testBindingFactoryWithBadTypes()
    {
        $this->dashboard->bind([], 'tacos');
    }
}
