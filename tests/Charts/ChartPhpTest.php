<?php

namespace Khill\Lavacharts\Tests\Charts;

use Khill\Lavacharts\Charts\Chart;
use Khill\Lavacharts\DataTables\DataTable;
use Khill\Lavacharts\Support\Options;
use Khill\Lavacharts\Tests\ProvidersTestCase;
use Mockery;

class ChartPhpTest extends ProvidersTestCase
{
    /**
     * @dataProvider chartTypeProvider
     * @param string $chartType
     */
    public function testCreatingChartsManually($chartType)
    {
        $chartFQN = static::CHART_NAMESPACE.$chartType;

        /** @var Chart $chart */
        $chart = new $chartFQN('TestChart');

        $this->assertInstanceOf(Chart::class, $chart);
        $this->assertEquals('TestChart', $chart->getLabel());
        $this->assertEquals($chartType, $chart->getType());
    }

    /**
     * @dataProvider chartTypeProvider
     * @param string $chartType
     */
    public function testCreatingChartsManuallyWithDataTable($chartType)
    {
        $chartFQN = static::CHART_NAMESPACE.$chartType;

        /** @var Chart $chart */
        $chart = new $chartFQN('TestChart', Mockery::mock(DataTable::class));

        $this->assertInstanceOf(DataTable::class, $chart->getDataTable());
    }

    /**
     * @dataProvider chartTypeProvider
     * @param string $chartType
     */
    public function testCreatingChartsManuallyWithDataTableAndOptions($chartType)
    {
        $chartFQN = static::CHART_NAMESPACE.$chartType;

        /** @var Chart $chart */
        $chart = new $chartFQN('TestChart', Mockery::mock(DataTable::class), [
            'title' => 'Chart!'
        ]);

        $this->assertInstanceOf(Options::class, $chart->getOptions());
        $this->assertEquals('Chart!', $chart->getOption('title'));
    }

    /**
     * @dataProvider chartTypeProvider
     * @param string $chartType
     */
    public function testChartToArray($chartType)
    {
        $datatable = Mockery::mock(DataTable::class)
            ->shouldReceive('hasFormattedColumns')
            ->once()
            ->andReturn(false)
            ->getMock();

        $chartFQN = static::CHART_NAMESPACE.$chartType;

        /** @var Chart $chart */
        $chart = new $chartFQN('TestChart', $datatable, [
            'title' => 'Chart!'
        ]);

        $chartArr = $chart->toArray();

        $this->assertTrue(is_array($chartArr));
        $this->assertArrayHasKey('options', $chartArr);
        $this->assertArrayHasKey('title', $chartArr['options']);
        $this->assertEquals('Chart!', $chartArr['options']['title']);
    }
}
