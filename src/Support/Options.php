<?php

namespace Khill\Lavacharts\Support;

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
 * @property string datetime_format
 * @property string pngOutput
 */
class Options extends ArrayObject implements Arrayable, Jsonable
{
    use ArrayToJson;

    /**
     * Customization options.
     *
     * @var array
     */
    protected $options;

    /**
     * Create a new Options object from an array or another Options object.
     *
     * @param array|Options $options
     * @return Options
     */
    public static function create($options)
    {
        if ($options instanceof Options) {
            $options = $options->toArray();
        }

        return new static($options);
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
     * Customizable constructor.
     *
     * @public
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * When treating options as a string, assume they are getting serialized.
     *
     * @public
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Retrieve options as member properties
     *
     * @public
     * @param  string $option
     * @return mixed
     * @throws \Khill\Lavacharts\Exceptions\UndefinedOptionException
     */
    public function __get($option)
    {
        if (! $this->has($option)) {
            throw new UndefinedOptionException($option);
        }

        return $this->options[$option];
    }

    /**
     * Setting an option by assigning to the class property.
     *
     * @public
     * @param string $option
     * @param mixed  $value
     */
    public function __set($option, $value)
    {
        $this->options[$option] = $value;
    }

    /**
     * Return the string name of the property that will be used for ArrayAccess
     *
     * @public
     * @return string
     */
    public function getArrayAccessProperty()
    {
        return 'options';
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
     * Check if an option has been set.
     *
     * @param string $option
     * @return bool
     */
    public function hasAndIs($option, $type)
    {
        return $this->has($option) && call_user_func("is_$type", $this->get($option));
    }

    /**
     * Get the value of an option.
     *
     * @param string $option Option to get
     * @return mixed|null
     * @throws UndefinedOptionException
     */
    public function get($option)
    {
        if (! $this->has($option)) {
            throw new UndefinedOptionException($option);
        }

        return $this->options[$option];
    }

    /**
     * Sets the value of the given option.
     *
     * @param string $option
     * @param mixed  $value
     */
    public function set($option, $value)
    {
        $this->options[$option] = $value;
    }

    /**
     * Sets the value of the given option if it does not exist.
     *
     * Value is left unchanged if it is already set.
     *
     * @param  string $option
     * @param  mixed  $value
     * @return bool True if the value was set, otherwise false.
     */
    public function setIfNot($option, $value)
    {
        if (! $this->has($option)) {
            $this->options[$option] = $value;

            return true;
        }

        return false;
    }

    /**
     * Remove an option from the set.
     *
     * @param  string $option
     * @return self
     * @throws UndefinedOptionException If th
     */
    public function forget($option)
    {
        if (! $this->has($option)) {
            throw new UndefinedOptionException($option);
        }

        unset($this->options[$option]);

        return $this;
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
     * Returns the options without the options specified.
     *
     * @param array $options
     * @return array
     */
    public function without(array $options)
    {
        return array_diff_key($this->options, $options);
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
