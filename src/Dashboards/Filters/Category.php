<?php

namespace Khill\Lavacharts\Dashboards\Filters;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * Category Filter Class
 *
 * A picker to choose one or more between a set of defined values.
 *
 * @package    Lavacharts
 * @subpackage Dashboards\Filters
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 * @see        https://developers.google.com/chart/interactive/docs/gallery/controls#googlevisualizationcategoryfilter
 */
class Category extends Filter
{
    /**
     * Type of Filter.
     *
     * @var string
     */
    const TYPE = 'CategoryFilter';

    /**
     * Category specific default options.
     *
     * @var array
     */
    private $defaults = [
        'values',
        'useFormattedValue'
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
        $options = new ConfigOptions($this->defaults);

        parent::__construct($columnLabelOrIndex, $options, $config);
    }
}
