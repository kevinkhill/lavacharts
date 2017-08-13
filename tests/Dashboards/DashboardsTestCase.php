<?php

namespace Khill\Lavacharts\Tests\Dashboards;

use Khill\Lavacharts\Charts\Chart;
use Khill\Lavacharts\Dashboards\Wrappers\ChartWrapper;
use Khill\Lavacharts\Dashboards\Wrappers\ControlWrapper;
use Khill\Lavacharts\Tests\ProvidersTestCase;
use Mockery;

class DashboardsTestCase extends ProvidersTestCase
{
    /**
     * @var Chart
     */
    protected $mockChart;

    /**
     * @var ChartWrapper
     */
    protected $mockChartWrap;

    /**
     * @var ControlWrapper
     */
    protected $mockControlWrap;

    public function setUp()
    {
        parent::setUp();

        $this->mockChartWrap = Mockery::mock(ChartWrapper::class);

        $this->mockControlWrap = Mockery::mock(ControlWrapper::class);
    }
}
