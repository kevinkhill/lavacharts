<?php

namespace Khill\Lavacharts\Tests\DataTables;

use BadMethodCallException;
use Carbon\Carbon;
use Khill\Lavacharts\Charts\AnnotationChart;
use Khill\Lavacharts\Charts\AreaChart;
use Khill\Lavacharts\Charts\BarChart;
use Khill\Lavacharts\Charts\BubbleChart;
use Khill\Lavacharts\Charts\CalendarChart;
use Khill\Lavacharts\Charts\CandlestickChart;
use Khill\Lavacharts\Charts\ColumnChart;
use Khill\Lavacharts\Charts\ComboChart;
use Khill\Lavacharts\Charts\GanttChart;
use Khill\Lavacharts\Charts\GaugeChart;
use Khill\Lavacharts\Charts\GeoChart;
use Khill\Lavacharts\Charts\HistogramChart;
use Khill\Lavacharts\Charts\LineChart;
use Khill\Lavacharts\Charts\OrgChart;
use Khill\Lavacharts\Charts\PieChart;
use Khill\Lavacharts\Charts\SankeyChart;
use Khill\Lavacharts\Charts\ScatterChart;
use Khill\Lavacharts\Charts\SteppedAreaChart;
use Khill\Lavacharts\Charts\TableChart;
use Khill\Lavacharts\Charts\TimelineChart;
use Khill\Lavacharts\Charts\TreeMapChart;
use Khill\Lavacharts\Charts\WordTreeChart;
use Khill\Lavacharts\DataTables\DataFactory;
use Khill\Lavacharts\DataTables\DataTable;

class ChartProvider
{
    /**
     * Return a test Chart by type.
     *
     * @param string                                 $type
     * @param \Khill\Lavacharts\DataTables\DataTable $data
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public static function make($type, DataTable $data)
    {
        if (method_exists(static::class, $type)) {
            return static::$type($data);
        } else {
            throw new BadMethodCallException('There is no test chart for "'.$type.'"');
        }
    }

    /**
     * @param \Khill\Lavacharts\DataTables\DataTable $data
     * @return \Khill\Lavacharts\Charts\AnnotationChart
     */
    public static function AnnotationChart(DataTable $data)
    {
        return new AnnotationChart('MyAnnotationChart', $data, [
            'elementId' => 'MyAnnotationChart',
            'displayAnnotations' => true
        ]);
    }

    /**
     * @param \Khill\Lavacharts\DataTables\DataTable $data
     * @return \Khill\Lavacharts\Charts\AreaChart
     */
    public static function AreaChart(DataTable $data)
    {
        return new AreaChart('MyAreaChart', $data, [
            'elementId' => 'MyAreaChart',
            'legend' => [
                'position' => 'inner'
            ],
            'chartArea'=> [
                'left' => 50,
                'width' => '90%'
            ],
        ]);
    }

    /**
     * @param \Khill\Lavacharts\DataTables\DataTable $data
     * @return \Khill\Lavacharts\Charts\BarChart
     */
    public static function BarChart(DataTable $data)
    {
        return new BarChart('MyBarChart', $data, [
            'elementId' => 'MyBarChart',
            'legend' => [
                'position' => 'top'
            ]
        ]);
    }

    /**
     * @param \Khill\Lavacharts\DataTables\DataTable $data
     * @return \Khill\Lavacharts\Charts\BubbleChart
     */
    public static function BubbleChart(DataTable $data)
    {
        return new BubbleChart('MyBubbleChart', $data, [
            'elementId' => 'MyBubbleChart',
            'width' => 400,
            'height' => 400,
            'chartArea' => [
                'width' => 400
            ],
            'title' => 'Correlation between life expectancy, fertility rate ' .
                'and population of some world countries (2010)',
            'hAxis' => [
                'title' => 'Life Expectancy'
            ],
            'vAxis' => [
                'title' => 'Fertility Rate'
            ],
            'bubble' => [
                'textStyle' => [
                    'fontSize' => 11
                ]
            ]
        ]);
    }

    /**
     * @param \Khill\Lavacharts\DataTables\DataTable $data
     * @return \Khill\Lavacharts\Charts\CalendarChart
     */
    public static function CalendarChart(DataTable $data)
    {
        return new CalendarChart('MyCalendarChart', $data, [
            'elementId' => 'MyCalendarChart',
            'title' => 'Cars Sold',
            'unusedMonthOutlineColor' => [
                'stroke'        => '#ECECEC',
                'strokeOpacity' => 0.75,
                'strokeWidth'   => 1
            ],
            'dayOfWeekLabel' => [
                'color' => '#4f5b0d',
                'fontSize' => 16,
                'italic' => true
            ],
            'noDataPattern' => [
                'color' => '#DDD',
                'backgroundColor' => '#11FFFF'
            ],
            'colorAxis' => [
                'values' => [0, 100],
                'colors' => ['black', 'green']
            ]
        ]);
    }

    /**
     * @param \Khill\Lavacharts\DataTables\DataTable $data
     * @return \Khill\Lavacharts\Charts\CandlestickChart
     */
    public static function CandlestickChart(DataTable $data)
    {
        return new CandlestickChart('MyCandlestickChart', $data, [
            'elementId' => 'MyCandlestickChart',
            'legend' => 'none',
            'height' => 400
        ]);
    }

    /**
     * @param \Khill\Lavacharts\DataTables\DataTable $data
     * @return \Khill\Lavacharts\Charts\ColumnChart
     */
    public static function ColumnChart(DataTable $data)
    {
        return new ColumnChart('MyColumnChart', $data, [
            'elementId' => 'MyColumnChart',
            'title' => 'Company Performance',
            'titleTextStyle' => [
                'color' => '#eb6b2c',
                'fontSize' => 14
            ]
        ]);
    }

    /**
     * @param \Khill\Lavacharts\DataTables\DataTable $data
     * @return \Khill\Lavacharts\Charts\ComboChart
     */
    public static function ComboChart(DataTable $data)
    {
        return new ComboChart('MyComboChart', $data, [
            'elementId' => 'MyComboChart',
            'title' => 'Company Performance',
            'titleTextStyle' => [
                'color' => 'rgb(123, 65, 89)',
                'fontSize' => 16,
                'bold' => true
            ],
            'legend' => [
                'position' => 'in'
            ],
            'seriesType' => 'bars',
            'series' => [
                2 => [
                    'type' => 'line'
                ]
            ]
        ]);
    }

    /**
     * @param \Khill\Lavacharts\DataTables\DataTable $data
     * @return \Khill\Lavacharts\Charts\GanttChart
     */
    public static function GanttChart(DataTable $data)
    {
        return new GanttChart('MyGanttChart', $data, [
            'elementId' => 'MyGanttChart',
            'height' => 275
        ]);
    }

    /**
     * @param \Khill\Lavacharts\DataTables\DataTable $data
     * @return \Khill\Lavacharts\Charts\GaugeChart
     */
    public static function GaugeChart(DataTable $data)
    {
        return new GaugeChart('MyGaugeChart', $data, [
            'elementId' => 'MyGaugeChart',
            'greenFrom' => 0,
            'greenTo' => 69,
            'yellowFrom' => 70,
            'yellowTo' => 89,
            'redFrom' => 90,
            'redTo' => 100,
            'majorTicks' => [
                'Safe',
                'Critical'
            ]
        ]);
    }

    /**
     * @param \Khill\Lavacharts\DataTables\DataTable $data
     * @return \Khill\Lavacharts\Charts\GeoChart
     */
    public static function GeoChart(DataTable $data)
    {
        return new GeoChart('MyGeoChart', $data, [
            'elementId' => 'MyGeoChart',
            'width' => 500,
            'height' => 300,
        ]);
    }

    /**
     * @param \Khill\Lavacharts\DataTables\DataTable $data
     * @return \Khill\Lavacharts\Charts\HistogramChart
     */
    public static function HistogramChart(DataTable $data)
    {
        return new HistogramChart('MyHistogramChart', $data, [
            'title' => 'Lengths of dinosaurs, in meters',
            'legend' => 'none'
        ]);
    }

    /**
     * @param \Khill\Lavacharts\DataTables\DataTable $data
     * @return \Khill\Lavacharts\Charts\LineChart
     */
    public static function LineChart(DataTable $data)
    {
        return new LineChart('MyLineChart', $data, [
            'elementId' => 'my-chart',
            'title' => 'Weather in October',
            'width' => '100%',
            'chartArea'=> [
                'left' => 50,
                'width' => '70%'
            ],
            'backgroundColor' => [
                'strokeWidth' => 6,
                'fill'        => '#A9D0F5',
                'stroke'      => '#007D7D',
            ]
        ]);
    }

    /**
     * @param \Khill\Lavacharts\DataTables\DataTable $data
     * @return \Khill\Lavacharts\Charts\OrgChart
     */
    public static function OrgChart(DataTable $data)
    {
        return new OrgChart('MyOrgChart', $data, [
            'elementId' => 'MyOrgChart',
            'title' => 'Company Performance',
            'allowHtml' => true,
            'titleTextStyle' => [
                'color' => '#eb6b2c',
                'fontSize' => 14
            ]
        ]);
    }

    /**
     * @param \Khill\Lavacharts\DataTables\DataTable $data
     * @return \Khill\Lavacharts\Charts\PieChart
     */
    public static function PieChart(DataTable $data)
    {
        return new PieChart('MyPieChart', $data, [
            'title' => 'Reasons I visit IMDB',
            'is3D' => true,
            'slices' => [
                ['offset' => 0.2],
                ['offset' => 0.25],
                ['offset' => 0.3]
            ]
        ]);
    }

    /**
     * @param \Khill\Lavacharts\DataTables\DataTable $data
     * @return \Khill\Lavacharts\Charts\SankeyChart
     */
    public static function SankeyChart(DataTable $data)
    {
        $colors = [
            '#a6cee3',
            '#b2df8a',
            '#fb9a99',
            '#fdbf6f',
            '#cab2d6',
            '#ffff99',
            '#1f78b4',
            '#33a02c'
        ];

        return new SankeyChart('MySankeyChart', $data, [
            'legend' => [
                'position' => 'none'
            ],
            'sankey' => [
                'node' => [
                    'colors' => $colors
                ],
                'link' => [
                    'colorMode' => 'gradient',
                    'colors' => $colors
                ]
            ]
        ]);
    }

    /**
     * @param \Khill\Lavacharts\DataTables\DataTable $data
     * @return \Khill\Lavacharts\Charts\ScatterChart
     */
    public static function ScatterChart(DataTable $data)
    {
        return new ScatterChart('MyScatterChart', $data, [
            'title' => 'Age vs. Weight comparison',
            'hAxis' => [
                'title' => 'Age',
                'minValue' => 20,
                'maxValue' => 40
            ],
            'vAxis' => [
                'title' => 'Age',
                'minValue' => 100,
                'maxValue' => 300
            ],
            'legend' => [
                'position' => 'none'
            ]
        ]);
    }

    /**
     * @param \Khill\Lavacharts\DataTables\DataTable $data
     * @return \Khill\Lavacharts\Charts\SteppedAreaChart
     */
    public static function SteppedAreaChart(DataTable $data)
    {
        return new SteppedAreaChart('MySteppedAreaChart', $data, [
            'title' => 'The decline of \'The 39 Steps\'',
            'vAxis' => [
                'title' => 'Accumulated Rating'
            ],
            'isStacked' => true,
            'legend' => 'none'
        ]);
    }

    /**
     * @param \Khill\Lavacharts\DataTables\DataTable $data
     * @return \Khill\Lavacharts\Charts\TableChart
     */
    public static function TableChart(DataTable $data)
    {
        return new TableChart('MyTableChart', $data);
    }

    /**
     * @param \Khill\Lavacharts\DataTables\DataTable $data
     * @return \Khill\Lavacharts\Charts\TimelineChart
     */
    public static function TimelineChart(DataTable $data)
    {
        return new TimelineChart('MyTimelineChart', $data, [
            'title' => 'Classes',
            'timeline' => [
                'colorByRowLabel' => true
            ]
        ]);
    }

    /**
     * @param \Khill\Lavacharts\DataTables\DataTable $data
     * @return \Khill\Lavacharts\Charts\TreeMapChart
     */
    public static function TreeMapChart(DataTable $data)
    {
        return new TreeMapChart('MyTreeMapChart', $data, [
            'elementId' => 'lavachart',
            'width' => 400,
            'height' => 200
        ]);
    }

    /**
     * @param \Khill\Lavacharts\DataTables\DataTable $data
     * @return \Khill\Lavacharts\Charts\WordTreeChart
     */
    public static function WordTreeChart(DataTable $data)
    {
        return new WordTreeChart('MyWordTreeChart', $data, [
            'wordtree' => [
                'format' => 'implicit',
                'word' => 'cats'
            ]
        ]);
    }
}
