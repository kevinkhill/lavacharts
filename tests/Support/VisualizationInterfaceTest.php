<?php

namespace Khill\Lavacharts\Tests\Charts;

use Khill\Lavacharts\Charts\ChartFactory;
use Khill\Lavacharts\Tests\ProvidersTestCase;

class VisualizationInterfaceTest extends ProvidersTestCase
{
    /**
     * Map of chart types to their visualization package.
     */
    const CHART_TYPE_PACKAGE_MAP = [
        'AnnotationChart'  => 'annotationchart',
        'AreaChart'        => 'corechart',
        'BarChart'         => 'corechart',
        'BubbleChart'      => 'corechart',
        'CalendarChart'    => 'calendar',
        'CandlestickChart' => 'corechart',
        'ColumnChart'      => 'corechart',
        'ComboChart'       => 'corechart',
        'DonutChart'       => 'corechart',
        'GanttChart'       => 'gantt',
        'GaugeChart'       => 'gauge',
        'GeoChart'         => 'geochart',
        'HistogramChart'   => 'corechart',
        'LineChart'        => 'corechart',
        'PieChart'         => 'corechart',
        'SankeyChart'      => 'sankey',
        'ScatterChart'     => 'corechart',
        'SteppedAreaChart' => 'corechart',
        'TableChart'       => 'table',
        'TimelineChart'    => 'timeline',
        'TreeMapChart'     => 'treemap',
        'WordTreeChart'    => 'wordtree',
    ];

    /**
     * Map of chart types to their visualization class name.
     */
    const CHART_TYPE_CLASS_MAP = [
        'AnnotationChart'  => 'google.visualization.AnnotationChart',
        'AreaChart'        => 'google.visualization.AreaChart',
        'BarChart'         => 'google.visualization.BarChart',
        'BubbleChart'      => 'google.visualization.BubbleChart',
        'CalendarChart'    => 'google.visualization.Calendar',
        'CandlestickChart' => 'google.visualization.CandlestickChart',
        'ColumnChart'      => 'google.visualization.ColumnChart',
        'ComboChart'       => 'google.visualization.ComboChart',
        'DonutChart'       => 'google.visualization.PieChart',
        'GanttChart'       => 'google.visualization.Gantt',
        'GaugeChart'       => 'google.visualization.Gauge',
        'GeoChart'         => 'google.visualization.GeoChart',
        'HistogramChart'   => 'google.visualization.Histogram',
        'LineChart'        => 'google.visualization.LineChart',
        'PieChart'         => 'google.visualization.PieChart',
        'SankeyChart'      => 'google.visualization.Sankey',
        'ScatterChart'     => 'google.visualization.ScatterChart',
        'SteppedAreaChart' => 'google.visualization.SteppedAreaChart',
        'TableChart'       => 'google.visualization.Table',
        'TimelineChart'    => 'google.visualization.Timeline',
        'TreeMapChart'     => 'google.visualization.TreeMap',
        'WordTreeChart'    => 'google.visualization.WordTree',
    ];

    /**
     * Map of chart types to their versions.
     */
    const CHART_TYPE_VERSION_MAP = [
        'AnnotationChart'  => '1',
        'AreaChart'        => '1',
        'BarChart'         => '1',
        'BubbleChart'      => '1',
        'CalendarChart'    => '1.1',
        'CandlestickChart' => '1',
        'ColumnChart'      => '1',
        'ComboChart'       => '1',
        'DonutChart'       => '1',
        'GanttChart'       => '1',
        'GaugeChart'       => '1',
        'GeoChart'         => '1',
        'HistogramChart'   => '1',
        'LineChart'        => '1',
        'PieChart'         => '1',
        'SankeyChart'      => '1',
        'ScatterChart'     => '1',
        'SteppedAreaChart' => '1',
        'TableChart'       => '1',
        'TimelineChart'    => '1',
        'TreeMapChart'     => '1',
        'WordTreeChart'    => '1',
    ];

    private function makeChart($type)
    {
        return ChartFactory::create($type, [md5($type), null]);
    }

    private function getPackageFromType($type)
    {
        return static::CHART_TYPE_PACKAGE_MAP[$type];
    }

    private function getClassFromType($type)
    {
        return static::CHART_TYPE_CLASS_MAP[$type];
    }

    private function getVersionFromType($type)
    {
        return static::CHART_TYPE_VERSION_MAP[$type];
    }

    /**
     * @dataProvider chartTypeProvider
     * @covers Chart::getType()
     * @param string $chartType
     */
    public function testGetType($chartType)
    {
        $chart = $this->makeChart($chartType);

        $this->assertEquals($chartType, $chart->getType());
    }

    /**
     * @dataProvider chartTypeProvider
     * @covers Chart::getJsPackage()
     * @param string $chartType
     */
    public function testGetJsPackage($chartType)
    {
        $chart = $this->makeChart($chartType);

        $this->assertEquals($this->getPackageFromType($chartType), $chart->getJsPackage());
    }

    /**
     * @dataProvider chartTypeProvider
     * @covers Chart::getJsClass()
     * @param string $chartType
     */
    public function testGetJsClass($chartType)
    {
        $chart = $this->makeChart($chartType);

        $this->assertEquals($this->getClassFromType($chartType), $chart->getJsClass());
    }

    /**
     * @dataProvider chartTypeProvider
     * @covers Chart::getVersion()
     * @param string $chartType
     */
    public function testGetVersion($chartType)
    {
        $chart = $this->makeChart($chartType);

        $this->assertEquals($this->getVersionFromType($chartType), $chart->getVersion());
    }
}
