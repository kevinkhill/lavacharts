<?php

namespace Khill\Lavacharts\Configs\UIs;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Options;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * ChartRangeUI Object
 *
 * Customization for ChartRange Filters in Dashboards.
 *
 * @package    Khill\Lavacharts
 * @subpackage Configs\UIs
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class ChartRangeUI extends UI
{
    /**
     * Type of UI object
     *
     * @var string
     */
    const TYPE = 'ChartRangeUI';

    /**
     * Default options available.
     *
     * @var array
     */
    private $chartRangeDefaults = [
        'chartType',
        'chartOptions',
        'chartView',
        'minRangeSize',
        'snapToData'
    ];

    /**
     * Creates a new ChartRangeUI object
     *
     * @param  array $config
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function __construct($config = [])
    {
        $options = new Options($this->defaults);
        $options->extend($this->chartRangeDefaults);
        $options->remove([
            'label',
            'labelSeparator',
            'labelStacking',
            'cssClass'
        ]);

        parent::__construct($options, $config);
    }

    /**
     * The type of the chart drawn inside the control.
     * Accepted values are:
     * - 'AreaChart'
     * - 'LineChart'
     * - 'ComboChart'
     * - 'ScatterChart'
     *
     * @param  string $chartType
     * @return \Khill\Lavacharts\Configs\UIs\ChartRangeUI
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function chartType($chartType)
    {
        $values = [
            'AreaChart',
            'LineChart',
            'ComboChart',
            'ScatterChart'
        ];

        return $this->setStringInArrayOption(__FUNCTION__, $chartType, $values);
    }

    /**
     *
     */
    public function chartOptions()
    {
        //TODO: Decide on implementation
    }

    /**
     *
     */
    public function chartView()
    {
        //TODO: Decide on implementation
    }

    /**
     * The minimum selectable range size (range.end - range.start), specified in data value units.
     *
     * For a numeric axis, it is a number (not necessarily an integer).
     * For a date, datetime or timeofday axis, it is an integer that specifies the difference in milliseconds.
     *
     * @param  int|float $minRangeSize Data value difference interpreted as 1 pixel
     * @return \Khill\Lavacharts\Configs\UIs\ChartRangeUI
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function minRangeSize($minRangeSize)
    {
        return $this->setNumericOption(__FUNCTION__, $minRangeSize);
    }

    /**
     * If true, range thumbs are snapped to the nearest data points.
     *
     * In this case, the end points of the range returned by getState() are necessarily values in the data table.
     *
     * @param  boolean $snapToData
     * @return \Khill\Lavacharts\Configs\UIs\ChartRangeUI
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function snapToData($snapToData)
    {
        return $this->setBoolOption(__FUNCTION__, $snapToData);
    }
}
