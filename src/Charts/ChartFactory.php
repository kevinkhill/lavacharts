<?php

namespace Khill\Lavacharts\Charts;

use Khill\Lavacharts\Exceptions\InvalidChartType;
use Khill\Lavacharts\Exceptions\InvalidDataTable;
use Khill\Lavacharts\Support\Args;
use Khill\Lavacharts\Support\Contracts\DataInterface;

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
     * Types of charts that can be created.
     *
     * @var array
     */
    const TYPES = [
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
     * Create new chart from type with DataTable and config passed
     * from the main Lavacharts class.
     *
     * @param  string $type Type of chart to create.
     * @param  array  $args Passed arguments from __call in Lavacharts.
     * @return \Khill\Lavacharts\Charts\Chart
     * @throws \Khill\Lavacharts\Exceptions\InvalidChartType
     * @throws \Khill\Lavacharts\Exceptions\InvalidDataTable
     */
    public static function create($type, $args)
    {
        if (! self::isValidChart($type)) {
            throw new InvalidChartType($type);
        }

        $chartBuilder = new ChartBuilder;

        $args = new Args($args);

        list($label, $data, $options) = $args;

        $chartBuilder->setType($type);

        if ($data !== null && $data instanceof DataInterface === false) {
            throw new InvalidDataTable($data);
        }

        $chartBuilder->setLabel($label);
        $chartBuilder->setDatatable($data);

        if (isset($options)) {
            if (is_string($options)) {
                $chartBuilder->setElementId($options);
            }

            if (is_array($options)) {
                if (array_key_exists('elementId', $options)) {
                    $chartBuilder->setElementId($options['elementId']);
                    unset($options['elementId']);
                }

                if (array_key_exists('png', $options)) {
                    $chartBuilder->setPngOutput($options['png']);
                    unset($options['png']);
                }

                if (array_key_exists('material', $options)) {
                    $chartBuilder->setMaterialOutput($options['material']);
                    unset($options['material']);
                }

                $chartBuilder->setOptions($options);
            }
        }

        return $chartBuilder->getChart();
    }

    /**
     * Returns the array of supported chart types.
     *
     * @since  3.1.0
     * @return array
     */
    public static function getChartTypes()
    {
        return static::TYPES;
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
        return in_array($type, static::TYPES, true);
    }
}
