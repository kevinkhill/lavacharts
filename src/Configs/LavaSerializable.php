<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\Exceptions\InvalidConfigValue;
use \Khill\Lavacharts\Exceptions\InvalidConfigProperty;

/**
 * JsonConfig Object
 *
 * The parent object for all config objects. Adds JsonSerializable and methods for setting options.
 *
 *
 * @package    Khill\Lavacharts
 * @since      3.1.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class JsonOptions implements \JsonSerializable
{

    /**
     * Holds all the customization options
     *
     * @var \Khill\Lavacharts\Configs\Options
     */
    protected $options;

    /**
     * Retrieves the Options object from the chart.
     *
     * @access public
     * @param \Khill\Lavacharts\Configs\Options $options
     */
    public function setOptions(Options $options)
    {
        return $this->options;
    }

    /**
     * Retrieves the Options object from the chart.
     *
     * @access public
     * @return \Khill\Lavacharts\Configs\Options
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Custom serialization of the JsonConfig object.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->options;
    }
}
