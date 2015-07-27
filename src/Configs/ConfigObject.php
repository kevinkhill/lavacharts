<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Configs\Options;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;
use \Khill\Lavacharts\Exceptions\InvalidConfigProperty;

/**
 * ConfigObject Parent Class
 *
 * The base class for the individual configuration objects, providing common
 * functions to the child objects.
 *
 *
 * @package    Lavacharts
 * @subpackage Configs
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class ConfigObject
{
    /**
     * Name of the ConfigObject, no namespace
     *
     * @var string
     */
    protected $className;

    /**
     * Allowed keys for the Options child objects.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Builds the Options object.
     *
     * Passing an array of key value pairs will set the configuration for each
     * child object created from this parent object.
     *
     * @param  mixed                 $child  Child ConfigOption object.
     * @param  array                 $config Array of options.
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     * @return mixed
     */
    public function __construct($child, $config)
    {
        $class = new \ReflectionClass($child);

        $this->className = $class->getShortname();
        $this->options = array_map(function ($prop) {
            return $prop->name;
        }, $class->getProperties(\ReflectionProperty::IS_PUBLIC));

        if (is_array($config) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'array',
                'with valid keys as '.Utils::arrayToPipedString($this->options)
            );
        }

        foreach ($config as $option => $value) {
            if (in_array($option, $this->options)) {
                $this->{$option}($value);
            } else {
                throw new InvalidConfigProperty(
                    $this->className,
                    __FUNCTION__,
                    $option,
                    $this->options
                );
            }
        }
    }

    /**
     * Returns an array representation of the object.
     *
     * If passed a label, then the array will be returned with the label as the
     * key.
     *
     * Called with no label returns an array with the classname as the key.
     *
     * @param  string $keyName Key name to be applied to generated array.
     * @return array  Assoc. array of the options of the object.
     */
    public function toArray($keyName = null)
    {
        $output = [];

        foreach ($this->options as $option) {
            if (isset($this->{$option})) {
                $output[$option] = $this->{$option};
            }
        }

        if (is_null($keyName)) {
            return [lcfirst($this->className) => $output];
        } else {
            return [$keyName => $output];
        }
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
     * function for easy creation of exceptions
     */
    protected function invalidConfigValue($func, $type, $extra = '')
    {
        if (! empty($extra)) {
            return new InvalidConfigValue(
                $this->className . '::' . $func,
                $type,
                $extra
            );
        } else {
            return new InvalidConfigValue(
                $this->className . '::' . $func,
                $type
            );
        }
    }
}
