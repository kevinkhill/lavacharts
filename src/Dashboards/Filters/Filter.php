<?php

namespace Khill\Lavacharts\Dashboards\Filters;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Configs\ConfigOptions;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;
use \Khill\Lavacharts\Exceptions\InvalidConfigProperty;

/**
 * Filter Parent Class
 *
 * The base class for the individual filter objects, providing common
 * functions to the child objects.
 *
 *
 * @package    Lavacharts
 * @subpackage Dashbaords\Filters
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class Filter implements \JsonSerializable
{
    /**
     * Default configuration options.
     *
     * @var array
     */
    protected $baseDefaults = [
        'filterColumnIndex',
        'filterColumnLabel',
        'ui'
    ];

    /**
     * Allowed keys for the ConfigOptions child objects.
     *
     * @var \Khill\Lavacharts\Configs\ConfigOptions
     */
    protected $options;

    /**
     * Builds a new Filter Object
     *
     * @param $columnLabelOrIndex
     * @param ConfigOptions $options
     * @param $config
     * @throws InvalidConfigProperty
     * @throws InvalidConfigValue
     * @return self
     */
    public function __construct($columnLabelOrIndex, ConfigOptions $options, $config)
    {
        $this->options = $options->extend($this->baseDefaults);

        if (is_string($columnLabelOrIndex) === false && is_int($columnLabelOrIndex) === false) {
            throw new InvalidConfigValue(
                static::TYPE,
                __FUNCTION__,
                'string|int'
            );
        }

        if (is_string($columnLabelOrIndex) === true) {
            $config['filterColumnLabel'] = $columnLabelOrIndex;
        }

        if (is_int($columnLabelOrIndex) === true) {
            $config['filterColumnIndex'] = $columnLabelOrIndex;
        }

        foreach ($config as $option => $value) {
            if ($this->options->has($option) === false) {
                throw new InvalidConfigProperty(
                    static::TYPE,
                    __FUNCTION__,
                    $option,
                    $this->options->toArray()
                );
            }

            call_user_func([$this, $option], $value);
        }
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
    public function filterColumnIndex($columnIndex)
    {
        if (is_int($columnIndex) === false) {
            throw new InvalidConfigValue(
                get_class(),
                __FUNCTION__,
                'integer'
            );
        }

        $this->options->set(__FUNCTION__, $columnIndex);

        return $this;
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
    public function filterColumnLabel($columnLabel)
    {
        if (Utils::nonEmptyString($columnLabel) === false) {
            throw new InvalidConfigValue(
                get_class(),
                __FUNCTION__,
                'string'
            );
        }

        $this->options->set(__FUNCTION__, $columnLabel);

        return $this;
    }

    /**
     * @param $option
     * @return null
     * @throws InvalidConfigProperty
     */
    public function __get($option)
    {
        if (in_array($option, $this->options->getValues($option)) === false) {
            throw new InvalidConfigProperty(
                get_class(),
                __FUNCTION__,
                $option,
                $this->getDefaults()
            );
        }

        return $this->options->get($option);
    }

    function jsonSerialize()
    {
        return $this->options->getValues();
    }
}
