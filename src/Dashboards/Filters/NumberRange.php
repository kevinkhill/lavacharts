<?php

namespace Khill\Lavacharts\Dashboards\Filters;

use \Khill\Lavacharts\Configs\Options;
use Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * Number Range Filter Class
 *
 * @package    Lavacharts
 * @subpackage Dashboards\Filters
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 * @see        https://developers.google.com/chart/interactive/docs/gallery/controls#--googlevisualizationnumberrangefilter
 */
class NumberRange extends Filter
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
    private $defaults = [
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
     * @return self
     */
    public function __construct($columnLabelOrIndex, $config = [])
    {
        parent::__construct($columnLabelOrIndex, $this->defaults, $config);
    }

    /**
     * Minimum allowed value for the range lower extent.
     *
     * If undefined, the value will be inferred from the contents of the DataTable managed by the control.
     *
     * @param  int|float $minValue
     * @throws InvalidConfigValue
     * @return self
     */
    public function minValue($minValue)
    {
        if (is_numeric($minValue) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'int|float'
            );
        }

        return $this->setOption(__FUNCTION__, $minValue);
    }

    /**
     * Maximum allowed value for the range higher extent.
     *
     * If undefined, the value will be inferred from the contents of the DataTable managed by the control.
     *
     * @param  int|float $maxValue
     * @throws InvalidConfigValue
     * @return self
     */
    public function maxValue($maxValue)
    {
        if (is_numeric($maxValue) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'int|float'
            );
        }

        return $this->setOption(__FUNCTION__, $maxValue);
    }
}
