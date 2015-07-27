<?php namespace Khill\Lavacharts\Formats;

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

use Khill\Lavacharts\Utils;
use Khill\Lavacharts\Exceptions\InvalidConfigValue;
use Khill\Lavacharts\Exceptions\InvalidConfigProperty;

class Format
{
    /**
     * Allowed keys for the ConfigOptions child objects.
     *
     * @var array
     */
    protected $options = array();

    /**
     * Builds the ConfigOptions object.
     *
     * Passing an array of key value pairs will set the configuration for each
     * child object created from this parent object.
     *
     * @param  mixed                 $child  Child ConfigOption object.
     * @param  array                 $config Array of options.
     * @throws InvalidConfigValue
     * @throws InvalidConfigProperty
     * @return mixed
     */
    public function __construct($child, $config)
    {
        $class = new \ReflectionClass($child);

        $this->options = array_map(function ($prop) {
            return $prop->name;
        }, $class->getProperties(\ReflectionProperty::IS_PUBLIC));

        if (is_array($config)) {
            foreach ($config as $option => $value) {
                if (in_array($option, $this->options)) {
                    $this->{$option}($value);
                } else {
                    throw new InvalidConfigProperty(
                        $child::TYPE,
                        __FUNCTION__,
                        $option,
                        $this->options
                    );
                }
            }
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'array',
                'with valid keys as '.Utils::arrayToPipedString($this->options)
            );
        }
    }

    /**
     * Same as toArray, but without the class name as a key to being multi-dimension.
     *
     * @return array Array of the options of the object.
     */
    public function getValues()
    {
        $output = array();

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
