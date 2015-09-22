<?php

namespace Khill\Lavacharts\Dashboards\Filters;

use \Khill\Lavacharts\Options;

/**
 * Date Range Class
 *
 * @package    Khill\Lavacharts
 * @subpackage Dashboards\Filters
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 * @see        https://developers.google.com/chart/interactive/docs/gallery/controls#googlevisualizationdaterangefilter
 */
class DateRangeFilter extends Filter
{
    /**
     * Type of Filter.
     *
     * @var string
     */
    const TYPE = 'DateRangeFilter';

    /**
     * DateRange specific default options.
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
}
