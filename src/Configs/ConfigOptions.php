<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\Exceptions\InvalidConfigProperty;

class ConfigOptions
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
     * @param $defaults
     * @return $this
     */
    public function extend($defaults)
    {
        $this->defaults = array_merge($this->defaults, $defaults);

        return $this;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     * @throws InvalidConfigProperty
     */
    public function set($option, $value)
    {
        if(in_array($option, $this->defaults) === false) {
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
     * @internal param $name
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
