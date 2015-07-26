<?php

namespace Khill\Lavacharts\Dashboards\Filters;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * Category Filter Class
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
class Category extends Filter
{
    /**
     * Type of Filter.
     *
     * @var string
     */
    const TYPE = 'CategoryFilter';

    /**
     * Index of the column to filter.
     *
     * @var integer
     */
    public $filterColumnIndex;

    /**
     * Label of the column to filter.
     *
     * @var string
     */
    public $filterColumnLabel;

    /**
     * List of values to choose from.
     *
     * @var array
     */
    public $values;

    /**
     * Use cell values or formatted values.
     *
     * @var boolean
     */
    public $useFormattedValue;

    /**
     * UI object to configure the display of the control.
     *
     * @var \Khill\Lavacharts\Configs\UI
     */
    public $ui;

    /**
     * Creates the new Filter object to filter the given column label.
     *
     * @param string $columnLabel
     */
    public function __construct($columnLabel)
    {
        parent::__construct($columnLabel);
    }

    /**
     * The column of the datatable the filter should operate upon.
     *
     * It is mandatory to provide either this option or filterColumnLabel.
     * If both present, this option takes precedence.
     *
     * @param  integer $index Column index
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function filterColumnIndex($index)
    {
        if (is_int($index) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'integer'
            );
        }

        $this->filterColumnIndex = $index;
    }

    /**
     * The label of the column the filter should operate upon.
     *
     * It is mandatory to provide either this option or filterColumnIndex.
     * If both present, filterColumnIndex takes precedence.
     *
     * @param  integer $label Column label
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function filterColumnLabel($label)
    {
        if (Utils::nonEmptyString($label) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        $this->filterColumnIndex = $label;
    }
}
