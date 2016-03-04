<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\Exceptions\InvalidOptions;

/**
 * Options Object
 *
 * An object that contains a set of options for customizing aspects
 * of charts, dashboards and formats.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Configs
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2016, KHill Designs
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
    private $values;

    /**
     * Create a new Options object with a set of options.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->values = $config;
    }

    /**
     * Retrieves all of the set options
     *
     * @since  3.1.0
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }


    /**
     * Batch set options from an array.
     *
     * @param  array $config Options to set
     * @throws \Khill\Lavacharts\Exceptions\InvalidOptions
     */
    public function set(array $config)
    {
        $this->values = $config;
    }

    /**
     * Merges two Options objects and combines the options and options.
     *
     * @param  array|\Khill\Lavacharts\Configs\Options $config
     * @throws \Khill\Lavacharts\Exceptions\InvalidOptions
     */
    public function merge($config)
    {
        if ($config instanceof Options) {
            $config = $config->getValues();
        }

        if (is_array($config) === false) {
            throw new InvalidOptions($config);
        }

        $this->values = array_merge($this->values, $config);
    }

    /**
     * Allows for the options to be traversed with foreach.
     *
     * @since  3.1.0
     * @implements IteratorAggregate
     * @return int
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->values);
    }

    /**
     * Custom serialization of the Options object.
     *
     * @implements JsonSerializable
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->values;
    }

    /**
     * @implements ArrayAccess
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->values[] = $value;
        } else {
            $this->values[$offset] = $value;
        }
    }

    /**
     * @implements ArrayAccess
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->values[$offset]);
    }

    /**
     * @implements ArrayAccess
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->values[$offset]);
    }

    /**
     * @implements ArrayAccess
     * @param mixed $offset
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return isset($this->values[$offset]) ? $this->values[$offset] : null;
    }
}
