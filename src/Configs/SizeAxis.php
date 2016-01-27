<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\JsonConfig;
use \Khill\Lavacharts\Options;

/**
 * SizeAxis Object
 *
 * An object containing all the values for the sizeAxis which can be
 * passed into the chart's options.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Configs
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class SizeAxis extends JsonConfig
{
    /**
     * Type of JsonConfig object
     *
     * @var string
     */
    const TYPE = 'SizeAxis';

    /**
     * Default options for SizeAxis
     *
     * @var array
     */
    private $defaults = [
        'maxSize',
        'maxValue',
        'minSize',
        'minValue'
    ];

    /**
     * Builds the configuration when passed an array of options.
     *
     * All options can be set by either passing an array with associative
     * values for option => value, or by chaining together the functions once
     * an object has been created.
     *
     * @param  array $config An array containing configuration options.
     * @return \Khill\Lavacharts\Configs\SizeAxis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function __construct($config = [])
    {
        $options = new Options($this->defaults);

        parent::__construct($options, $config);
    }

    /**
     * Sets maximum radius of the largest possible bubble, in pixels.
     *
     * @param  int $maxSize
     * @return \Khill\Lavacharts\Configs\SizeAxis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function maxSize($maxSize)
    {
        return $this->setIntOption(__FUNCTION__, $maxSize);
    }

    /**
     * Set the size value (as appears in the chart data) to be mapped to
     * $this->maxSize.
     *
     * Larger values will be cropped to this value.
     *
     * @param  int $maxValue
     * @return \Khill\Lavacharts\Configs\SizeAxis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function maxValue($maxValue)
    {
        return $this->setIntOption(__FUNCTION__, $maxValue);
    }

    /**
     * Sets minimum radius of the smallest possible bubble, in pixels
     *
     * @param  int $minSize
     * @return \Khill\Lavacharts\Configs\SizeAxis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function minSize($minSize)
    {
        return $this->setIntOption(__FUNCTION__, $minSize);
    }

    /**
     * Set the size value (as appears in the chart data) to be mapped to
     * $this->minSize.
     *
     * Larger values will be cropped to this value.
     *
     * @param  int
     * @return \Khill\Lavacharts\Configs\SizeAxis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function minValue($minValue)
    {
        return $this->setIntOption(__FUNCTION__, $minValue);
    }
}
