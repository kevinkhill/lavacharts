<?php namespace Khill\Lavacharts\Events;

/**
 * Event Parent Object
 *
 * The base class for the individual event objects, providing common
 * functions to the child objects.
 *
 *
 * @package    Lavacharts
 * @subpackage Events
 * @since      v2.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2014, KHill Designs
 * @link       http://github.com/kevinkhill/LavaCharts GitHub Repository Page
 * @link       http://kevinkhill.github.io/LavaCharts GitHub Project Page
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Helpers\Helpers as h;
use Khill\Lavacharts\Exceptions\InvalidConfigValue;
use Khill\Lavacharts\Exceptions\InvalidConfigProperty;

class Event
{
    /**
     * Name of the Event Object
     *
     * @var string
     */
    protected $className;

    /**
     * Allowed keys for the Event child objects.
     *
     * @var array
     */
    protected $options = array();

    /**
     * Builds the Event object.
     *
     * Passing an array of key value pairs will set the configuration for each
     * child object created from this parent object.
     *
     * @param  array Array of options.
     *
     * @throws InvalidConfigValue
     * @throws InvalidConfigProperty
     * @return object
     */
    public function __construct($config)
    {
        $class = new \ReflectionClass($child);

        $this->className = $class->getShortname();
        $this->options = array_map(function ($prop) {
            return $prop->name;
        }, $class->getProperties(\ReflectionProperty::IS_PUBLIC));

        if (is_array($config)) {
            foreach ($config as $option => $value) {
                if (in_array($option, $this->options)) {
                    $this->$option($value);
                } else {
                    throw new InvalidConfigProperty(
                        $this->className,
                        __FUNCTION__,
                        $option,
                        $this->options
                    );
                }
            }
        } else {
            throw new InvalidConfigValue(
                $this->className,
                __FUNCTION__,
                'array',
                'with valid keys as '.h::arrayToPipedString($this->options)
            );
        }
    }

    public function setCallback($c)
    {
        if (is_string($c) && ! empty($c))
        {
            $this->callback = $c;
        } else {
            throw new InvalidConfigValue(
                $this->className,
                __FUNCTION__,
                'string'
            );
        }
    }
}
