<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\Exceptions\InvalidConfigProperty;
use Khill\Lavacharts\Exceptions\InvalidConfigValue;

class Options
{
    /**
     * @var array
     */
    private $defaults = [];

    /**
     * @var array
     */
    private $values = [];

    /**
     * @param $defaults
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

    public function has($option)
    {
        return in_array($option, $this->defaults);
    }

    /**
     * @param $option
     * @return null
     * @throws InvalidConfigProperty
     */
    public function get($option)
    {
        if(in_array($option, $this->values) === false) {
            throw new InvalidConfigProperty(
                get_class(),
                __FUNCTION__,
                $option,
                array_keys($this->values)
            );
        }

        return $this->values[$option];
    }

    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * Returns an array representation of the object.
     *
     * @return array Array of the defined options.
     */
    public function getValues()
    {
        return $this->values;
    }
}
