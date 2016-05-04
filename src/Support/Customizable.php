<?php

namespace Khill\Lavacharts\Support;

/**
 * Class Customizable
 *
 * Any class that extends this class is defined as customizable, which means that it
 * has options, which can be set to configure / customize the final result once rendered.
 *
 *
 * @package    Khill\Lavacharts\Configs
 * @since      3.0.5
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2016, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT      MIT
 */
class Customizable implements \ArrayAccess, \IteratorAggregate, \JsonSerializable
{
    /**
     * Customization options.
     *
     * @var array
     */
    private $options;

    /**
     * Customizable constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * Sets the values of the customized class with an array of key value pairs.
     *
     * @param string $option
     * @param mixed  $value
     */
    public function setOption($option, $value)
    {
        $this->options[$option] = $value;
    }

    /**
     * Sets the values of the customized class with an array of key value pairs.
     *
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * Retrieves all of the set options
     *
     * @since  3.1.0
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Allows for the options to be traversed with foreach.
     *
     * @since  3.1.0
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->options);
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

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->options[] = $value;
        } else {
            $this->options[$offset] = $value;
        }
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->options[$offset]);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->options[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return isset($this->options[$offset]) ? $this->options[$offset] : null;
    }
}
