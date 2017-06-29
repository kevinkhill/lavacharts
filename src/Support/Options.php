<?php

namespace Khill\Lavacharts\Support;

use Countable;
use Khill\Lavacharts\Exceptions\InvalidOptions;
use Khill\Lavacharts\Exceptions\UndefinedOptionException;
use Khill\Lavacharts\Support\Contracts\Arrayable;
use Khill\Lavacharts\Support\Contracts\Jsonable;
use Khill\Lavacharts\Support\Traits\ArrayToJsonTrait as ArrayToJson;

/**
 * Options Class
 *
 * Lightweight wrapper for what would be an array of options. Useful for converting
 * to json or chaining from what has set options.
 *
 *
 * @package       Khill\Lavacharts\Support
 * @since         3.2.0
 * @author        Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link          http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link          http://lavacharts.com                   Official Docs Site
 * @license       http://opensource.org/licenses/MIT      MIT
 *
 * @property string elementId
 * @property string pngOutput
 */
class Options implements Arrayable, Jsonable, Countable
{
    use ArrayToJson;

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
     * Returns the default configuration options for Lavacharts
     *
     * @return array
     */
    public static function getDefault()
    {
        return require(__DIR__.'/../Laravel/config/lavacharts.php');
    }

    /**
     * Returns a list of the options that can be set.
     *
     * @return array
     */
    public static function getAvailable()
    {
        return array_keys(self::getDefault());
    }

    /**
     * Retrieve options as member properties
     *
     * @param  string $option
     * @return mixed
     * @throws \Khill\Lavacharts\Exceptions\UndefinedOptionException
     */
    function __get($option)
    {
        if ( ! $this->has($option)) {
            throw new UndefinedOptionException($option);
        }

        return $this->options[$option];
    }

    /**
     * Setting an option by assigning to the class property.
     *
     * @param string $option
     * @param mixed  $value
     */
    public function __set($option, $value)
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
     * When treating options as a string, assume they are getting serialized.
     *
     * @return string
     */
    function __toString()
    {
        return $this->toJson();
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
     * Merge a set of options with the existing options.
     *
     * @param array $options
     */
    public function merge($options)
    {
        if ($options instanceof Options) {
            $options = $options->toArray();
        }

        $this->options = array_merge($this->options, $options);
    }

    /**
     * Count the number of options
     *
     * @return int
     */
    public function count()
    {
        return count($this->options);
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
}
