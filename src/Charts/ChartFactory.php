<?php

namespace Khill\Lavacharts\Charts;

use \Khill\Lavacharts\Builders\ChartBuilder;
use Khill\Lavacharts\DataTables\DataTable;
use \Khill\Lavacharts\Exceptions\InvalidDataTable;

/**
 * ChartFactory Class
 *
 * Used for creating new charts and removing the need for the main Lavacharts
 * class to handle the creation.
 *
 *
 * @category  Class
 * @package   Khill\Lavacharts\Charts
 * @since     3.1.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class ChartFactory
{
    /**
     * Instance of the ChartBuilder for, well, building charts.
     *
     * @var \Khill\Lavacharts\Builders\ChartBuilder
     */
    private $chartBuilder;

    /**
     * Types of charts that can be created.
     *
     * @var array
     */
    public static $CHART_TYPES = [
        'AreaChart',
        'AnnotationChart',
        'BarChart',
        'BubbleChart',
        'CalendarChart',
        'CandlestickChart',
        'ColumnChart',
        'ComboChart',
        'DonutChart',
        'GanttChart',
        'GaugeChart',
        'GeoChart',
        'HistogramChart',
        'LineChart',
        'PieChart',
        'SankeyChart',
        'ScatterChart',
        'SteppedAreaChart',
        'TableChart',
        'TimelineChart',
        'TreeMapChart',
        'WordTreeChart',
    ];

    /**
     * ChartFactory constructor.
     */
    public function __construct()
    {
        $this->chartBuilder = new ChartBuilder;
    }

    /**
     * ChartFactory constructor.
     */
    public static function build($type, $args)
    {
        return (new self)->create($type, $args);
    }

    /**
     * Create new chart from type with DataTable and config passed
     * from the main Lavacharts class.
     *
     * @param  string $type Type of chart to create.
     * @param  array  $args Passed arguments from __call in Lavacharts.
     * @return \Khill\Lavacharts\Charts\Chart
     * @throws \Khill\Lavacharts\Exceptions\InvalidChartType
     * @throws \Khill\Lavacharts\Exceptions\InvalidDataTable
     */
    public function create($type, $args)
    {
        if ($args[1] !== null && $args[1] instanceof DataTable === false) {
            throw new InvalidDataTable;
        }

        $this->chartBuilder->setType($type)
                           ->setLabel($args[0])
                           ->setDatatable($args[1]);

        if (isset($args[2])) {
            if (is_string($args[2])) {
                $this->chartBuilder->setElementId($args[2]);
            }

            if (is_array($args[2])) {
                if (array_key_exists('elementId', $args[2])) {
                    $this->chartBuilder->setElementId($args[2]['elementId']);
                    unset($args[2]['elementId']);
                }

                if (array_key_exists('png', $args[2])) {
                    $this->chartBuilder->setPngOutput($args[2]['png']);
                    unset($args[2]['png']);
                }

                if (array_key_exists('material', $args[2])) {
                    $this->chartBuilder->setMaterialOutput($args[2]['material']);
                    unset($args[2]['material']);
                }

                $this->chartBuilder->setOptions($args[2]);
            }
        }

        if (isset($args[3])) {
            $this->chartBuilder->setElementId($args[3]);
        }

        return $this->chartBuilder->getChart();
    }

    /**
     * Returns the array of supported chart types.
     *
     * @since  3.1.0
     * @return array
     */
    public static function getChartTypes()
    {
        return static::$CHART_TYPES;
    }

    /**
     * Returns the array of supported chart types.
     *
     * @since  3.1.0
     * @param  string $type Type of chart to check.
     * @return bool
     */
    public static function isValidChart($type)
    {
        return in_array($type, self::$CHART_TYPES, true);
    }
}
