<?php

namespace Khill\Lavacharts\Support;

use ArrayAccess;
use JsonSerializable;
use Khill\Lavacharts\Support\Contracts\Arrayable;
use Khill\Lavacharts\Support\Contracts\Jsonable;

/**
 * Options Class
 *
 * Lightweight wrapper for what would be an array of options. Useful for converting
 * to json or chaining from what has set options.
 *
 *
 * @package       Khill\Lavacharts\Support
 * @since         3.2
 * @author        Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link          http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link          http://lavacharts.com                   Official Docs Site
 * @license       http://opensource.org/licenses/MIT      MIT
 */
class Options implements Arrayable, Jsonable, JsonSerializable
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
     * Retrieve options as member properties
     *
     * @param $option
     * @return mixed|null
     */
    function __get($option)
    {
        return $this->has($option) ? $this->options[$option] : null;
    }

    /**
     * Get the value of an option.
     *
     * @param string $key Option to get
     * @return mixed|null
     */
    public function get($key)
    {
        if ($this->has($key)) {
            return $this->options[$key];
        }

        return null;
    }

    /**
     * Sets the values of the customized class with an array of key value pairs.
     *
     * @param string $option
     * @param mixed  $value
     */
    public function set($option, $value)
    {
        $this->options[$option] = $value;
    }

    /**
     * Check if an option has been set.
     *
     * @param string $option
     * @return bool
     */
    public function has($option)
    {
        return array_key_exists($option, $this->options);
    }

    /**
     * Merge a set of options with the existing options.
     *
     * @param array $options
     */
    public function merge($options)
    {
        if ($options instanceof Options) {
            $this->options = array_merge($this->options, $options->toArray());
        }

        if (is_array($options)) {
            $this->options = array_merge($this->options, $options);
        }
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->options;
    }

    /**
     * Returns a customize JSON representation of an object.
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this);
    }

    /**
     * Custom serialization of the Options object.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
