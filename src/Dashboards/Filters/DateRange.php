<?php

namespace Khill\Lavacharts\Dashboards\Filters;

use \Khill\Lavacharts\Configs\Options;

/**
 * Date Range Class
 *
 * @package    Lavacharts
 * @subpackage Dashboards\Filters
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 * @see        https://developers.google.com/chart/interactive/docs/gallery/controls#googlevisualizationdaterangefilter
 */
class DateRange extends Filter
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
    private $childDefaults = [
        'minValue',
        'maxValue'
    ];

    /**
     * Creates the new Filter object to filter the given column label or index.
     *
     * @param $columnLabelOrIndex
     * @param array $config
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function __construct($columnLabelOrIndex, $config=[])
    {
        $options = new Options($this->defaults);

        parent::__construct($columnLabelOrIndex, $options, $config);
    }
}
