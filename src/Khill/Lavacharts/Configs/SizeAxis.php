<?php namespace Khill\Lavacharts\Configs;

/**
 * SizeAxis Object
 *
 * An object containing all the values for the sizeAxis which can be
 * passed into the chart's options.
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

use Khill\Lavacharts\Exceptions\InvalidConfigValue;

class SizeAxis extends ConfigOptions
{
    /**
     * @var int Maximum radius of the largest possible bubble, in pixels.
     */
    public $maxSize;

    /**
     * @var int The size value to be mapped to $this->maxSize.
     */
    public $maxValue;

    /**
     * @var int Mininum radius of the smallest possible bubble, in pixels.
     */
    public $minSize;

    /**
     * @var int The size value to be mapped to $this->minSize.
     */
    public $minValue;


    /**
     * Builds the configuration when passed an array of options.
     *
     * All options can be set by either passing an array with associative
     * values for option => value, or by chaining together the functions once
     * an object has been created.
     *
     * @param  array An array containing configuration options.
     *
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigProperty
     * @return Khill\Lavacharts\Configs\SizeAxis
     */
    public function __construct($config = array())
    {
        parent::__construct($this, $config);
    }

    /**
     * Sets maximum radius of the largest possible bubble, in pixels
     *
     * @param int $maxSize
     *
     * @return Khill\Lavacharts\Configs\SizeAxis
     */
    public function maxSize($maxSize)
    {
        if (is_numeric($maxSize)) {
            $this->maxSize = $maxSize;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'int | float'
            );
        }

        return $this;
    }

    /**
     * Set the size value (as appears in the chart data) to be mapped to
     * $this->maxSize. Larger values will be cropped to this value.
     *
     * @param int $maxValue
     *
     * @return Khill\Lavacharts\Configs\SizeAxis
     */
    public function maxValue($maxValue)
    {
        if (is_numeric($maxValue)) {
            $this->maxValue = $maxValue;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'int | float'
            );
        }

        return $this;
    }

    /**
     * Sets mininum radius of the smallest possible bubble, in pixels
     *
     * @param int $minSize
     *
     * @return Khill\Lavacharts\Configs\SizeAxis
     */
    public function minSize($minSize)
    {
        if (is_numeric($minSize)) {
            $this->minSize = $minSize;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'int | float'
            );
        }

        return $this;
    }

    /**
     * Set the size value (as appears in the chart data) to be mapped to
     * $this->minSize. Larger values will be cropped to this value.
     *
     * @param int $minValue
     *
     * @return Khill\Lavacharts\Configs\SizeAxis
     */
    public function minValue($minValue)
    {
        if (is_numeric($minValue)) {
            $this->minValue = $minValue;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'int | float'
            );
        }

        return $this;
    }
}
