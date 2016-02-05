<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\Exceptions\InvalidConfigValue;
use \Khill\Lavacharts\Exceptions\InvalidOption;
use \Khill\Lavacharts\Exceptions\InvalidOptions;

/**
 * Options Object
 *
 * An object that contains a set of options, with methods for
 * extending, removing, getting and setting the values.
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
class Options implements \IteratorAggregate, \JsonSerializable
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

        $this->options = $options;
    }

    /**
     * Allows for the options to be traversed with foreach.
     *
     * @access public
     * @since  3.1.0
     * @return \Khill\Lavacharts\Options
     */
    public function set($option, $value)
    {
        $this->options[$option] = $value;

        return $this;
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
     * Get the value of a set option.
     *
     * @access public
     * @param  string $option Name of option.
     * @return mixed
     * @throws \Khill\Lavacharts\Exceptions\InvalidOption
     */
    public function get($option)
    {
        if (Utils::nonEmptyString($option) === false) {
            throw new InvalidOption($option, $this->options);
        }

        if (array_key_exists($option, $this->options) === false) {
            return null;
        } else {
            return $this->options[$option];
        }
    }

    /**
     * Returns the array of options that can be set.
     *
     * @access public
     * @return array
     */
    public function getAll()
    {
        return $this->options;
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
                'Options->setOptions',
                'array'
            );
        }

        foreach ($options as $option => $value) {
            if ($check === true) {
                $this->set($option, $value);
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
        $this->extend($options->getDefaults());

        return $this->setOptions($options->getoptions());
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

        $this->options = array_merge($this->options, $options);

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

        $this->options = array_merge(array_diff($this->options, $options));

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
}
