<?php

namespace Khill\Lavacharts\Dashboards\Filters;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Options;
use \Khill\Lavacharts\JsonConfig;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;


/**
 * Filter Parent Class
 *
 * The base class for the individual filter objects, providing common
 * functions to the child objects.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Dashbaords\Filters
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class Filter extends JsonConfig
{
    /**
     * Default configuration options.
     *
     * @var array
     */
    protected $defaults = [
        'filterColumnIndex',
        'filterColumnLabel',
        'ui'
    ];

    /**
     * Builds a new Filter Object
     * Takes either a column label or a column index to filter. The options object will be
     * created internally, so no need to set defaults. The child filter objects will set them.
     *
     * @param  \Khill\Lavacharts\Options $options
     * @param  array                     $columnLabelOrIndex
     * @param  array                     $config Array of options to set.
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function __construct(Options $options, $columnLabelOrIndex, $config = [])
    {
        if (is_array($config) === false) {
            throw new InvalidConfigValue(
                static::TYPE . '->' . __FUNCTION__,
                'array'
            );
        }

        if (Utils::nonEmptyString($columnLabelOrIndex) === false && is_int($columnLabelOrIndex) === false) {
            throw new InvalidConfigValue(
                static::TYPE . '->' . __FUNCTION__,
                'string|int'
            );
        }

        if (is_string($columnLabelOrIndex) === true) {
            $config = array_merge($config, ['filterColumnLabel' => $columnLabelOrIndex]);
        }

        if (is_int($columnLabelOrIndex) === true) {
            $config = array_merge($config, ['filterColumnIndex' => $columnLabelOrIndex]);
        }

        parent::__construct($options, $config);
    }

    /**
     * Returns the Filter type.
     *
     * @return string
     */
    public function getType()
    {
        return static::TYPE;
    }

    /**
     * The column of the datatable the filter should operate upon.
     *
     * It is mandatory to provide either this option or filterColumnLabel.
     * If both present, this option takes precedence.
     *
     * @param  integer $columnIndex Column index
     * @return \Khill\Lavacharts\Dashboards\Filters\Filter
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function filterColumnIndex($columnIndex)
    {
        return $this->setIntOption(__FUNCTION__, $columnIndex);
    }

    /**
     * The label of the column the filter should operate upon.
     * It is mandatory to provide either this option or filterColumnIndex.
     * If both present, filterColumnIndex takes precedence.
     *
     * @param  string $columnLabel Column label
     * @return \Khill\Lavacharts\Dashboards\Filters\Filter
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function filterColumnLabel($columnLabel)
    {
        return $this->setStringOption(__FUNCTION__, $columnLabel);
    }

    /**
     * Assigns custom attributes to the controls that the filter is attached to.
     *
     * @param  array $uiConfig Array of options for configuring the UI
     * @return \Khill\Lavacharts\Dashboards\Filters\Filter
     */
    public function ui($uiConfig)
    {
        $uiClass  = '\\Khill\\Lavacharts\\Configs\\UIs\\';
        $uiClass .= str_replace('Filter', 'UI', static::TYPE);

        return $this->setOption(__FUNCTION__, new $uiClass($uiConfig));
    }
}
