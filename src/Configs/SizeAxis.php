<?php namespace Khill\Lavacharts\Configs;

/**
 * SizeAxis Object
 *
 * An object containing all the values for the sizeAxis which can be
 * passed into the chart's options.
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

use Khill\Lavacharts\Exceptions\InvalidConfigValue;

class SizeAxis extends ConfigObject
{
    /**
     * Maximum radius of the largest possible bubble, in pixels.
     *
     * @var int
     */
    public $maxSize;

    /**
     * The size value to be mapped to $this->maxSize.
     *
     * @var int
     */
    public $maxValue;

    /**
     * Mininum radius of the smallest possible bubble, in pixels.
     *
     * @var int
     */
    public $minSize;

    /**
     * The size value to be mapped to $this->minSize.
     *
     * @var int
     */
    public $minValue;


    /**
     * Builds the configuration when passed an array of options.
     *
     * All options can be set by either passing an array with associative
     * values for option => value, or by chaining together the functions once
     * an object has been created.
     *
     * @param  array                 $config An array containing configuration options.
     * @throws InvalidConfigValue
     * @throws InvalidConfigProperty
     * @return SizeAxis
     */
    public function __construct($config = array())
    {
        parent::__construct($this, $config);
    }

    /**
     * Sets maximum radius of the largest possible bubble, in pixels
     *
     * @param  int      $maxSize
     * @return SizeAxis
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
     * @param  int      $maxValue
     * @return SizeAxis
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
     * @param  int      $minSize
     * @return SizeAxis
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
     * @param  int      $minValue
     * @return SizeAxis
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
