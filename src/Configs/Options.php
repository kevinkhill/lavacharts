<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\Exceptions\InvalidConfigProperty;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;
use Khill\Lavacharts\Exceptions\InvalidOption;

/**
 * Options Object
 *
 * An object that contains a set of options, with methods for extending, removing setting and getting
 * values for these set options.
 *
 *
 * @package    Lavacharts
 * @subpackage Configs
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class Options
{
    /**
     * Default options that can be set.
     *
     * @var array
     */
    private $defaults = [];

    /**
     * Set options as key => value pairs.
     *
     * @var array
     */
    private $values = [];

    /**
     * Create a new Options object with a set of defaults.
     *
     * @param array $defaults
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * return self
     */
    public function __construct($defaults)
    {
        if (is_array($defaults) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'array'
            );
        }

        $this->defaults = $defaults;
    }

    /**
     * Returns the array of options that can be set.
     *
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * Returns an array representation of the options.
     *
     * @return array Array of the defined options.
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Checks to see if a given option is available to set.
     *
     * @param  $option string Name of option.
     * @return boolean
     */
    public function hasOption($option)
    {
        return in_array($option, $this->defaults, true);
    }

    /**
     * Checks to see if a given option is set.
     *
     * @param  $option string Name of option.
     * @return boolean
     */
    public function hasValue($option)
    {
        if (is_string($option)) {
            return array_key_exists($option, $this->values);
        } else {
            return false;
        }
    }

    /**
     * Extends the default options with more options.
     *
     * @param  array $defaults Array of options to extend the defaults.
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function extend($defaults)
    {
        if (is_array($defaults) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'array'
            );
        }

        $this->defaults = array_merge($this->defaults, $defaults);

        return $this;
    }

    /**
     * Removes options from the default options.
     *
     * @param  array $options Array of options to remove from the defaults.
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function remove($options)
    {
        if (is_array($options) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'array'
            );
        }

        $this->defaults = array_merge(array_diff($this->defaults, $options));

        return $this;
    }

    /**
     * Set the value of an option.
     *
     * @param  string $option Name of option to set.
     * @param  mixed $value Value to set the option to.
     * @throws \Khill\Lavacharts\Exceptions\InvalidOption
     * @return self
     */
    public function set($option, $value)
    {
        if ($this->hasOption($option) === false) {
            throw new InvalidOption($option, $this->defaults);
        }

        $this->values[$option] = $value;

        return $this;
    }

    /**
     * Get the value of a set option.
     *
     * @param  string $option Name of option.
     * @throws \Khill\Lavacharts\Exceptions\InvalidOption
     * @return mixed
     */
    public function get($option)
    {
        if ($this->hasValue($option) === false) {
            throw new InvalidOption($option, $this->defaults);
        }

        return $this->values[$option];
    }
}
