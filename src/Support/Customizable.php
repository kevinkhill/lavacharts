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
 * @copyright  (c) 2017, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT      MIT
 */
class Customizable implements \ArrayAccess, \JsonSerializable
{
    /**
     * Customization options.
     *
     * @var array
     */
    protected $options;

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
     * Allow the setting of options via named option methods
     *
     * This is to prevent BC breaks from anyone using this style
     * of setting options.
     *
     * @param string $method Option to set.
     * @param mixed  $arg    Value for the option.
     */
    public function __call($method, $arg)
    {
        if (is_array($arg) === false) {
            $this->options[$method] = $arg;
        } else {
            $this->options[$method] = $arg[0];
        }
    }

    /**
     * Sets the values of the customized class with an array of key value pairs.
     *
     * @param string $option
     * @param mixed  $value
     * @return self
     */
    public function setOption($option, $value)
    {
        $this->options[$option] = $value;

        return $this;
    }

    /**
     * Sets the values of the customized class with an array of key value pairs.
     *
     * @param array $options
     * @return self
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        if (method_exists($this, 'setExtendedAttributes')) {
            $this->setExtendedAttributes();
        }

        return $this;
    }

    /**
     * Merge a set of options with the existing options.
     *
     * @since 3.0.5
     * @param array $options
     * @return self
     */
    public function mergeOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    /**
     * Retrieves all of the set options
     *
     * @since  3.0.5
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Returns true / false depending if any options have been set.
     *
     * @since  3.1.0
     * @return bool
     */
    public function hasOptions()
    {
        return count($this->options) > 0;
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
