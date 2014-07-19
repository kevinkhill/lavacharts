<?php namespace Khill\Lavacharts\Configs;

/**
 * ConfigOptions Parent Object
 *
 * The base class for the individual configuration objects, providing common
 * functions to the child objects.
 *
 *
 * @category  Class
 * @package   Khill\Lavacharts\Configs
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2014, KHill Designs
 * @link      https://github.com/kevinkhill/LavaCharts GitHub Repository Page
 * @link      http://kevinkhill.github.io/LavaCharts/ GitHub Project Page
 * @license   http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Helpers\Helpers;
use Khill\Lavacharts\Exceptions\InvalidConfigValue;
use Khill\Lavacharts\Exceptions\InvalidConfigProperty;

class ConfigOptions
{
    /**
     * @var string Output of the configOptions object.
     */
    protected $output;

    /**
     * @var array Allowed keys for the configOptions child objects.
     */
    protected $options = array();

    /**
     * Builds the ConfigOptions object.
     *
     * Passing an array of key value pairs will set the configuration for each
     * child object created from this master object.
     *
     * @param  array Array of options.
     *
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigProperty
     * @return object
     */
    public function __construct($config)
    {
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
                __FUNCTION__,
                'array',
                'with valid keys as '.Helpers::arrayToPipedString($this->options)
            );
        }

        return $this;
    }

    /**
     * Returns an array representation of the object.
     *
     * If passed a label, then the array will be returned with the label as the
     * key.
     *
     * Called with no label returns an array with the classname as the key.
     *
     * @return array
     */
    public function toArray($keyName = null)
    {
        $this->output = array();

        foreach ($this->options as $option) {
            if (isset($this->$option)) {
                $this->output[$option] = $this->$option;
            }
        }

        if (is_null($keyName)) {
            return array($this->className => $this->output);
        } else {
            return array($keyName => $this->output);
        }
    }

    /**
     * Same as toArray, but without the class name as a key to being multi-dimension.
     *
     * @return array Array of the options of the object.
     */
    public function getValues()
    {
        $this->output = array();

        foreach ($this->options as $option) {
            if (isset($this->$option)) {
                $this->output[$option] = $this->$option;
            }
        }

        return $this->output;
    }
}
