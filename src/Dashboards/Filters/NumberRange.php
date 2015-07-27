<?php

namespace Khill\Lavacharts\Dashboards\Filters;

use \Khill\Lavacharts\Configs\ConfigOptions;

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
 */
class NumberRange extends Filter
{
    /**
     * Type of Filter.
     *
     * @var string
     */
    const TYPE = 'NumberRangeFilter';

    private $defaults = [
        'minValue',
        'maxValue'
    ];

    /**
     * Creates the new Filter object to filter the given column label.
     *
     * @param $columnLabelOrIndex
     * @param array $config
     * @throws InvalidConfigProperty
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @internal param string $columnLabel
     */
    public function __construct($columnLabelOrIndex, $config=[])
    {
        $options = new ConfigOptions($this->defaults);

        parent::__construct($columnLabelOrIndex, $options, $config);
    }
}
