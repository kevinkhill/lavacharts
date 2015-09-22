<?php

namespace Khill\Lavacharts\Dashboards\Filters;

use \Khill\Lavacharts\Options;


/**
 * Number Range Filter Class
 *
 * @package    Khill\Lavacharts
 * @subpackage Dashboards\Filters
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 * @see        https://developers.google.com/chart/interactive/docs/gallery/controls#--googlevisualizationnumberrangefilter
 */
class NumberRangeFilter extends Filter
{
    /**
     * Type of Filter.
     *
     * @var string
     */
    const TYPE = 'NumberRangeFilter';

    /**
     * NumberRange specific default options.
     *
     * @var array
     */
    private $extDefaults = [
        'minValue',
        'maxValue'
    ];

    /**
     * Creates the new Filter object to filter the given column label or index.
     *
     * @param  string|int $columnLabelOrIndex The column label or index to filter.
     * @param  array $config Array of options to set.
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function __construct($columnLabelOrIndex, $config = [])
    {
        $options = new Options($this->defaults);
        $options->extend($this->extDefaults);

        parent::__construct($options, $columnLabelOrIndex, $config);
    }

    /**
     * Minimum allowed value for the range lower extent.
     * If undefined, the value will be inferred from the contents of the DataTable managed by the control.
     *
     * @param  int|float $minValue
     * @return \Khill\Lavacharts\Dashboards\Filters\NumberRange
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function minValue($minValue)
    {
        return $this->setNumericOption(__FUNCTION__, $minValue);
    }

    /**
     * Maximum allowed value for the range higher extent.
     *
     * If undefined, the value will be inferred from the contents of the DataTable managed by the control.
     *
     * @param  int|float $maxValue
     * @return \Khill\Lavacharts\Dashboards\Filters\NumberRange
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function maxValue($maxValue)
    {
        return $this->setNumericOption(__FUNCTION__, $maxValue);
    }
}
