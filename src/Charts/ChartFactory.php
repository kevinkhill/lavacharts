<?php

namespace Khill\Lavacharts\Charts;

use \Khill\Lavacharts\Configs\Options;
use \Khill\Lavacharts\DataTables\DataTable;
use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Values\ElementId;
use \Khill\Lavacharts\Volcano;
use \Khill\Lavacharts\Exceptions\InvalidDataTable;
use \Khill\Lavacharts\Exceptions\InvalidLabel;

/**
 * ChartFactory Class
 *
 * Used for creating new charts and removing the need for the main Lavacharts
 * class to handle the creation.
 *
 *
 * @category   Class
 * @package    Khill\Lavacharts
 * @subpackage Charts
 * @since      3.1.0
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
    public static $CHART_TYPES = [
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
     * Create new chart from type with DataTable and config passed
     * from the main Lavacharts class.
     *
     * @param  string $type Type of chart to create.
     * @param  array  $args Passed arguments from __call in Lavacharts.
     * @throws \Khill\Lavacharts\Exceptions\InvalidLabel
     * @throws \Khill\Lavacharts\Exceptions\InvalidLavaObject
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function create($type, $args)
    {
        if (isset($args[1]) === false) {
            throw new InvalidDataTable;
        }

        $chartBuilder = new ChartBuilder;

        $chartBuilder->setType($type);
        $chartBuilder->setLabel($args[0]);
        $chartBuilder->setDatatable($args[1]);

        if (isset($args[2])) {
            if (is_string($args[2])) {
                $chartBuilder->setElementId($args[2]);
            }

            if (is_array($args[2])) {
                if (array_key_exists('elementId', $args[2])) {
                    $chartBuilder->setElementId($args[2]['elementId']);
                }

                $chartBuilder->setOptions($args[2]);
            }
        }

        if (isset($args[3])) {
            $chartBuilder->setElementId($args[3]);
        }

        $chart = $chartBuilder->getChart();

        return $this->volcano->store($chart);
    }

    /**
     * Returns the array of supported chart types.
     *
     * @access public
     * @since  3.1.0
     * @return array
     */
    public static function getChartTypes()
    {
        return static::$CHART_TYPES;
    }
}
