<?php

namespace Khill\Lavacharts\Charts;

use \Khill\Lavacharts\DataTables\DataTable;
use \Khill\Lavacharts\Configs\Options;
use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Values\ElementId;
use \Khill\Lavacharts\Volcano;
use \Khill\Lavacharts\Exceptions\InvalidDataTable;
use \Khill\Lavacharts\Exceptions\InvalidLabel;
use \Khill\Lavacharts\Exceptions\InvalidLavaObject;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;
use \Khill\Lavacharts\Exceptions\InvalidFilterObject;
use \Khill\Lavacharts\Exceptions\InvalidFunctionParam;
use \Khill\Lavacharts\Exceptions\InvalidDivDimensions;

/**
 * Lavacharts - A PHP wrapper library for the Google Chart API
 *
 *
 * @category   Class
 * @package    Khill\Lavacharts
 * @subpackage Charts
 * @since      3.0.1
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class ChartFactory
{
    /**
     * Holds all of the defined Charts and DataTables.
     *
     * @var Volcano
     */
    private $volcano;

    /**
     * Types of charts that can be created.
     *
     * @var array
     */
    private static $CHART_TYPES = [
        'AreaChart',
        'AnnotationChart',
        'BarChart',
        'BubbleChart',
        'CalendarChart',
        'CandlestickChart',
        'ColumnChart',
        'ComboChart',
        //'GanttChart',
        //@TODO: Gantt charts have to use the new gstatic loader.js so some refactoring of lava.js is in order :(
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
        'TreemapChart'
    ];

    /**
     * Create a new instance of the ChartFactory with the Volcano storage.
     *
     * @param \Khill\Lavacharts\Volcano $volcano
     */
    public function __construct(Volcano $volcano)
    {
        $this->volcano = $volcano;
    }

    /**
     * Magic function to reduce repetitive coding and create aliases.
     *
     * @param  string $method Name of method
     * @param  array  $args   Passed arguments
     * @throws \Khill\Lavacharts\Exceptions\InvalidLabel
     * @throws \Khill\Lavacharts\Exceptions\InvalidLavaObject
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function create($type, $args)
    {
        $datatable = $args[1];
        $label     = new Label($args[0]);
        $options   = new Options( isset($args[2]) ? $args[2] : [] );

        if (in_array($type, static::$CHART_TYPES) === false) {
            throw new InvalidChartType($type);
        }

        if ($this->volcano->checkChart($type, $label) === true) {
            return $this->volcano->getChart($type, $label);
        }

        $newChart = __NAMESPACE__ . '\\' . $type;

        $chart = new $newChart($label, $datatable, $options);

        $this->volcano->storeChart($chart);

        return $chart;
    }

    /**
     * Returns the array of supported chart types.
     *
     * @access public
     * @since  3.0.1
     * @return array
     */
    public static function chartTypes()
    {
        return static::$CHART_TYPES;
    }
}
