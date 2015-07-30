<?php

namespace Khill\Lavacharts\Dashboards\Filters;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Configs\Options;
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
     * Allowed keys for the Options child objects.
     *
     * @var \Khill\Lavacharts\Configs\Options
     */
    protected $options;

    /**
     * Builds a new Filter Object
     *
     * Takes either a column label or a column index to filter. The options object will be
     * created internally, so no need to set defaults. The child filter objects will set them.
     *
     * @param  mixed $columnLabelOrIndex Either column index or label of the column to filter.
     * @param  array $config Array of options to set.
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function __construct($columnLabelOrIndex, $defaults, $config)
    {
        $this->options = (new Options($this->baseDefaults))->extend($defaults);

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

        $this->parseConfig($config);
    }

    public function parseConfig($config)
    {
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
     * Allows for access to options as properties of the Filter.
     *
     * @param  string $option Name of option to get.
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     * @return mixed
     */
    public function __get($option)
    {
        if (in_array($option, $this->options->getValues()) === false) {
            throw new InvalidConfigProperty(
                get_class(),
                __FUNCTION__,
                $option,
                $this->getDefaults()
            );
        }

        return $this->options->get($option);
    }

    /**
     * The column of the datatable the filter should operate upon.
     *
     * It is mandatory to provide either this option or filterColumnLabel.
     * If both present, this option takes precedence.
     *
     * @param  integer $index Column index
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
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

        return $this->setOption(__FUNCTION__, $columnIndex);
    }

    /**
     * The label of the column the filter should operate upon.
     *
     * It is mandatory to provide either this option or filterColumnIndex.
     * If both present, filterColumnIndex takes precedence.
     *
     * @param  integer $label Column label
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
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

        return $this->setOption(__FUNCTION__, $columnLabel);
    }

    /**
     * Sets the value of an option.
     *
     * Used internally to check the values for their respective types and validity.
     *
     * @param  string $option Option to set.
     * @param  mixed $value Value of the option.
     * @return self
     */
    protected function setOption($option, $value)
    {
        $this->options->set($option, $value);

        return $this;
    }

    /**
     * Custom serialization of the Filter object.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->options->getValues();
    }
}
