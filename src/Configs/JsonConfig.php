<?php

namespace Khill\Lavacharts\Configs;

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
class LavaSerializable implements \JsonSerializable
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
            $this->setOptions($config);
        }
    }

    /**
     * Get the value of a set option via magic method.
     *
     * @param  string $option Name of option.
     * @return mixed
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function __get($option)
    {
        return $this->options[$option];
    }

    /**
     * Gets the Options object for the JsonConfig
     *
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
     * @param  string $option Option to set.
     * @param  mixed  $value Value of the option.
     * @return \Khill\Lavacharts\JsonConfig
     */
    public function setOption($option, $value)
    {
        $this->options[$option] = $value;

        return $this;
    }

    /**
     * Parses the config array by passing the values through each method to check
     * validity against if the option exists.
     *
     * @param  array $config
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function setOptions($config)
    {
        $this->options->setOptions($config);
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
