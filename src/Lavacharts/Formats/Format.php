<?php namespace Lavacharts\Formats;

/**
 * Format Parent Class
 *
 * The base class for the individual configuration objects, providing common
 * functions to the child objects.
 *
 *
 * @package    Lavacharts
 * @subpackage Formatters
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2014, KHill Designs
 * @link       http://github.com/kevinkhill/Lavacharts GitHub Repository Page
 * @link       http://kevinkhill.github.io/Lavacharts  GitHub Project Page
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Lavacharts\Helpers\Helpers as h;
use Lavacharts\Exceptions\InvalidConfigValue;
use Lavacharts\Exceptions\InvalidConfigProperty;

class Format
{
    /**
     * Type of Formatter
     *
     * @var string
     */
    public $type;

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

        $this->type = $class->getShortname();
        $this->options = array_map(function ($prop) {
            return $prop->name;
        }, $class->getProperties(\ReflectionProperty::IS_PUBLIC));

        if (is_array($config)) {
            foreach ($config as $option => $value) {
                if (in_array($option, $this->options)) {
                    $this->{$option}($value);
                } else {
                    throw new InvalidConfigProperty(
                        $this->type,
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
                'with valid keys as '.h::arrayToPipedString($this->options)
            );
        }
    }

    /**
     * Returns a JSON string representation of the object's properties.
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->options);
    }
}
