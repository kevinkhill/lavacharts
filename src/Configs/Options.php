<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\Exceptions\InvalidConfigProperty;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

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
     */
    public function __construct($defaults)
    {
        $this->defaults = $defaults;
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

        $this->defaults = array_diff($this->defaults, $options);

        return $this;
    }

    /**
     * Set the value of an option.
     *
     * @param  string $option Name of option to set.
     * @param  mixed $value Value to set the option to.
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     * @return self
     */
    public function set($option, $value)
    {
        if (in_array($option, $this->defaults) === false) {
            throw new InvalidConfigProperty(
                get_class(),
                __FUNCTION__,
                $option,
                $this->defaults
            );
        }

        $this->values[$option] = $value;

        return $this;
    }

    /**
     * Checks to see if a given option is available to set.
     *
     * @param  $option string Name of option.
     * @return boolean
     */
    public function has($option)
    {
        return in_array($option, $this->defaults);
    }

    /**
     * Get the value of a set option.
     *
     * @param  string $option Name of option.
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     * @return mixed
     */
    public function get($option)
    {
        if(array_key_exists($option, $this->values) === false) {
            throw new InvalidConfigProperty(
                get_class(),
                __FUNCTION__,
                $option,
                array_keys($this->values)
            );
        }

        return $this->values[$option];
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
}
