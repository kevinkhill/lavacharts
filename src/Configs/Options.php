<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\Exceptions\InvalidConfigValue;
use \Khill\Lavacharts\Exceptions\InvalidOption;
use \Khill\Lavacharts\Exceptions\InvalidOptions;

/**
 * Options Object
 *
 * An object that contains a set of options for customizing aspects
 * of charts and dashboards.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Configs
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class Options implements \ArrayAccess, \IteratorAggregate, \JsonSerializable
{
    /**
     * Customization options.
     *
     * @var array
     */
    private $options = [];

    /**
     * Create a new Options object with a set of options.
     *
     * @param array $options
     * @throws \Khill\Lavacharts\Exceptions\InvalidOptions
     */
    public function __construct($options)
    {
        if (is_array($options) === false) {
            throw new InvalidOptions($options);
        }

        $this->options= $options;
    }

    /**
     * Allows for the options to be traversed with foreach.
     *
     * @access public
     * @since  3.1.0
     * @implements IteratorAggregate
     * @return int
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->options);
    }

    /**
     * Batch set options from an array.
     *
     *
     * The check flag can be used to bypass valid option checking
     * (added for the customize chart method ported from the 2.5 branch)
     *
     * @access public
     * @param  array $options Options to set
     * @param  bool  $check Flag to check options against list of set options
     * @return \Khill\Lavacharts\Options
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidOption
     */
    public function setOptions($options, $check = true)
    {
        if (is_array($options) === false) {
            throw new InvalidConfigValue(
                'Options->options',
                'array'
            );
        }

        foreach ($options as $option => $value) {
            if ($check === true) {
                $this->options($option, $value);
            } else {
                $this->options[$option] = $value;
            }
        }

        return $this;
    }

    /**
     * Merges two Options objects and combines the options and options.
     *
     * @access public
     * @param  \Khill\Lavacharts\Options $options
     * @return \Khill\Lavacharts\Options
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function merge(Options $options)
    {
        $this->options($options->options());

        return $this->options($options->options());
    }

    /**
     * Extends the default options with more options.
     *
     * @access public
     * @param  array $options Array of options to extend the options.
     * @return \Khill\Lavacharts\Options
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function extend($options)
    {
        if (is_array($options) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'array'
            );
        }

        $this->options= array_merge($this->options, $options);

        return $this;
    }

    /**
     * Removes options from the default options.
     *
     * @access public
     * @param  array $options Array of options to remove from the options.
     * @return \Khill\Lavacharts\Options
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function remove($options)
    {
        if (is_array($options) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'array'
            );
        }

        $this->options= array_merge(array_diff($this->options, $options));

        return $this;
    }

    /**
     * Custom serialization of the Options object.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->options;
    }
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->options[] = $value;
        } else {
            $this->options[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->options[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->options[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->options[$offset]) ? $this->options[$offset] : null;
    }
}
