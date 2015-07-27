<?php

namespace Khill\Lavacharts\DataTables\Formats;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Configs\ConfigOptions;
use \Khill\Lavacharts\DataTables\DataTable;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;
use \Khill\Lavacharts\Exceptions\InvalidConfigProperty;

/**
 * Format Parent Class
 *
 * The base class for the individual configuration objects, providing common
 * functions to the child objects.
 *
 *
 * @package    Lavacharts
 * @subpackage Formats
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class Format
{
    /**
     * Default configuration options.
     *
     * @var array
     */
    protected $defaults = [
        'filterColumnIndex',
        'filterColumnLabel',
        'ui'
    ];

    /**
     * Allowed keys for the ConfigOptions child objects.
     *
     * @var \Khill\Lavacharts\Configs\ConfigOptions
     */
    protected $options;

    /**
     * Builds the ConfigOptions object.
     *
     * Passing an array of key value pairs will set the configuration for each
     * child object created from this parent object.
     *
     * @param  \Khill\Lavacharts\Configs\ConfigOptions $options Object with options.
     * @param  array $config Array of options to set.
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     * @return self
     */
    public function __construct(ConfigOptions $options, $config)
    {
        $this->options = $options->extend($this->defaults);

        foreach ($config as $option => $value) {
            if (in_array($option, $this->options)) {
                call_user_func([$this, $option], $value);
            } else {
                throw new InvalidConfigProperty(
                    static::TYPE,
                    __FUNCTION__,
                    $option,
                    $this->options
                );
            }
        }
        /*
                $class = new \ReflectionClass($ild);

                $this->options = array_map(function ($prop) {
                    return $prop->name;
                }, $class->getProperties(\ReflectionProperty::IS_PUBLIC));

                if (is_array($config) === false) {
                    throw new InvalidConfigValue(
                        __FUNCTION__,
                        'array',
                        'with valid keys as '.Utils::arrayToPipedString($this->options)
                    );
                }
        */
    }

    /**
     * Formats a DataTable column by index.
     *
     * @access public
     * @since  3.0.0
     * @param  \Khill\Lavacharts\DataTables\DataTable $datatable
     * @param  int $index Column index
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnIndex
     * @return
     */
    public function formatColumn(DataTable $datatable, $index)
    {
        $datatable->formatColumn($index, $this);
    }

    /**
     * Returns the type of format.
     *
     * @access public
     * @return string Format type.
     */
    public function getType()
    {
        return static::TYPE;
    }

    /**
     * Same as toArray, but without the class name as a key to being multi-dimension.
     *
     * @return array Array of the options of the object.
     */
    public function getValues()
    {
        $output = [];

        foreach ($this->options as $option) {
            if (isset($this->{$option})) {
                $output[$option] = $this->{$option};
            }
        }

        return $output;
    }

    /**
     * Returns a JSON string representation of the object's properties.
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->getValues());
    }
}
