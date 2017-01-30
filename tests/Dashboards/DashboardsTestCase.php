<?php

namespace Khill\Lavacharts\Tests\Dashboards;

use Khill\Lavacharts\Tests\Charts\MockChart;
use Khill\Lavacharts\Tests\ProvidersTestCase;

/**
 * @property \Mockery\Mock                            mockChartWrap
 * @property \Mockery\Mock                            mockControlWrap
 * @property \Khill\Lavacharts\Tests\Charts\MockChart mockChart
 */
class DashboardsTestCase extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockChart = new MockChart(
            \Mockery::mock('\Khill\Lavacharts\Values\Label', ['TestChart'])->makePartial(),
            $this->partialDataTable
        );

        $this->mockChartWrap = \Mockery::mock('\Khill\Lavacharts\Dashboards\Wrappers\ChartWrapper', [
            $this->mockChart,
            \Mockery::mock('\Khill\Lavacharts\Values\ElementId', ['chart-div'])->makePartial()
        ])->makePartial();

        $this->mockControlWrap = \Mockery::mock('\Khill\Lavacharts\Dashboards\Wrappers\ControlWrapper', [
            \Mockery::mock('\Khill\Lavacharts\Dashboards\Filters\NumberRangeFilter')->makePartial(),
            \Mockery::mock('\Khill\Lavacharts\Values\ElementId', ['control-div'])->makePartial()
        ])->makePartial();
    }
}
