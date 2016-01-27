<?php

namespace Khill\Lavacharts\Dashboards\Filters;

use \Khill\Lavacharts\Options;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * Category Filter Class
 *
 * A picker to choose one or more between a set of defined values.
 *
 * @package    Khill\Lavacharts
 * @subpackage Dashboards\Filters
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 * @see        https://developers.google.com/chart/interactive/docs/gallery/controls#googlevisualizationcategoryfilter
 */
class CategoryFilter extends Filter
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
    private $extDefaults = [
        'useFormattedValue',
        'values'
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
     * Selects whether to use the DataTable values or the formatted values.
     *
     * When populating the list of selectable values automatically from the DataTable
     * column this filter operates on, whether to use the actual cell values or their formatted values.
     *
     * @param  boolean $useFormattedValue
     * @return \Khill\Lavacharts\Dashboards\Filters\Category
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function useFormattedValue($useFormattedValue)
    {
        return $this->setBoolOption(__FUNCTION__, $useFormattedValue);
    }
    /**
     * List of values to choose from.
     *
     * If an array of Objects is used, they should have a suitable toString() representation
     * for display to the user. If null or undefined, the list of values will be automatically
     * computed from the values present in the DataTable column this control operates on.
     *
     * @param  array $values
     * @return \Khill\Lavacharts\Dashboards\Filters\Category
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function values($values)
    {
        if (is_array($values) === false) {
            throw new InvalidConfigValue(
                static::TYPE . '->' . __FUNCTION__,
                'array'
            );
        }

        return $this->setOption(__FUNCTION__, $values);
    }
}
