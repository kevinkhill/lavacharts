<?php

namespace Khill\Lavacharts;


use \Khill\Lavacharts\Exceptions\InvalidConfigValue;
use \Khill\Lavacharts\Exceptions\InvalidConfigProperty;

/**
 * JsonConfig Object
 *
 * The parent object for all config objects. Adds JsonSerializable and methods for setting options.
 *
 *
 * @package    Khill\Lavacharts
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class JsonConfig implements \JsonSerializable
{
    /**
     * Allowed options to set for the LavaObject.
     *
     * @var \Khill\Lavacharts\Options
     */
    protected $options;

    /**
     * Creates a new JsonConfig object
     *
     * @param  \Khill\Lavacharts\Options $options
     * @param  array                             $config
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function __construct(Options $options, $config = [])
    {
        $this->options = $options;

        if (is_array($config) === false) {
            throw new InvalidConfigValue(
                static::TYPE . '->' . __FUNCTION__,
                'array'
            );
        }

        if (empty($config) === false) {
            /**
             * Patching 3.1 behavior into 3.0
             *
             * Just realized that porting the customize method into here, will
             * implement the 3.1 behavior of setting any option, regardless
             * of each charts' "defaultOptions"
             */
            $this->options->setOptions($config, false);
        }
    }

    /**
     * Get the value of a set option via magic method.
     *
     * @access public
     * @param  string $option Name of option.
     * @return mixed
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function __get($option)
    {
        return $this->options->get($option);
    }

    /**
     * Coverall method for the use of chaining any option as a method
     * instead of using the constructor.
     *
     * @since  3.0.2
     * @param  string $method Option to set
     * @param  string $param Values for the option
     * @return \Khill\Lavacharts\JsonConfig
     */
    public function __call($method, $param)
    {
        return $this->setOption($method, $param);
    }

    /**
     * Gets the Options object for the JsonConfig
     *
     * @access public
     * @return \Khill\Lavacharts\Options
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Shortcut method to set the value of an option and return $this.
     *
     * In order to maintain backwards compatibility, ConfigObjects will be unwrapped.
     *
     * @access public
     * @param  string $option Option to set.
     * @param  mixed  $value Value of the option.
     * @return \Khill\Lavacharts\JsonConfig
     */
    public function setOption($option, $value)
    {
        $this->options->set($option, $value);

        return $this;
    }

    /**
     * Parses the config array by passing the values through each method to check
     * validity against if the option exists.
     *
     * @access public
     * @param  array $config
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function setOptions($config)
    {
        if (is_array($config) === false) {
            throw new InvalidConfigValue(
                static::TYPE . '->' . __FUNCTION__,
                'array'
            );
        }

        foreach ($config as $option => $value) {
            if ($this->options->hasOption($option) === false) {
                throw new InvalidConfigProperty(
                    static::TYPE,
                    __FUNCTION__,
                    $option,
                    $this->options->getDefaults()
                );
            }

            call_user_func([$this, $option], $value);
        }
    }

    /**
     * Sets the value of a string option.
     *
     * @param  string $option Option to set.
     * @param  string $value Value of the option.
     * @return \Khill\Lavacharts\JsonConfig
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidOption
     */
    protected function setStringOption($option, $value)
    {
        if (Utils::nonEmptyString($value) === false) {
            throw new InvalidConfigValue(
                static::TYPE . '->' . $option,
                'string'
            );
        }

        $this->options->set($option, $value);

        return $this;
    }

    /**
     * Sets the value of a string option from an array of choices.
     *
     * @param  string $option Option to set.
     * @param  string $value Value of the option.
     * @param  array  $validValues Array of valid values
     * @return \Khill\Lavacharts\JsonConfig
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidOption
     */
    protected function setStringInArrayOption($option, $value, $validValues = [])
    {
        if (Utils::nonEmptyStringInArray($value, $validValues) === false) {
            throw new InvalidConfigValue(
                static::TYPE . '->' . $option,
                'string',
                'Whose value is one of '.Utils::arrayToPipedString($validValues)
            );
        }

        $this->options->set($option, $value);

        return $this;
    }

    /**
     * Sets the value of an integer option.
     *
     * @param  string $option Option to set.
     * @param  int    $value Value of the option.
     * @return \Khill\Lavacharts\JsonConfig
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidOption
     */
    protected function setIntOption($option, $value)
    {
        if (is_int($value) === false) {
            throw new InvalidConfigValue(
                static::TYPE . '->' . $option,
                'int'
            );
        }

        $this->options->set($option, $value);

        return $this;
    }

    /**
     * Sets the value of an integer or float option.
     *
     * @param  string $option Option to set.
     * @param  int|float $value Value of the option.
     * @return \Khill\Lavacharts\JsonConfig
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidOption
     */
    protected function setNumericOption($option, $value)
    {
        if (is_numeric($value) === false) {
            throw new InvalidConfigValue(
                static::TYPE . '->' . $option,
                'int|float'
            );
        }

        $this->options->set($option, $value);

        return $this;
    }

    /**
     * Sets the value of an integer option.
     *
     * @param  string $option Option to set.
     * @param  int    $value Value of the option.
     * @return \Khill\Lavacharts\JsonConfig
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidOption
     */
    protected function setIntOrPercentOption($option, $value)
    {
        if (Utils::isIntOrPercent($value) === false) {
            throw new InvalidConfigValue(
                static::TYPE . '->' . $option,
                'int|string',
                'String only if representing a percent. "50%"'
            );
        }

        $this->options->set($option, $value);

        return $this;
    }

    /**
     * Sets the value of an boolean option.
     *
     * @param  string $option Option to set.
     * @param  bool   $value Value of the option.
     * @return \Khill\Lavacharts\JsonConfig
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidOption
     */
    protected function setBoolOption($option, $value)
    {
        if (is_bool($value) === false) {
            throw new InvalidConfigValue(
                static::TYPE . '->' . $option,
                'bool'
            );
        }

        $this->options->set($option, $value);

        return $this;
    }

    /**
     * Custom serialization of the JsonConfig object.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->options;
    }
}
