<?php namespace Khill\Lavacharts\Configs;
/**
 * sizeAxis Object
 *
 * An object containing all the values for the sizeAxis which can be
 * passed into the chart's options.
 *
 *
 * @author Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2013, KHill Designs
 * @link https://github.com/kevinkhill/Codeigniter-gCharts GitHub Repository Page
 * @link http://kevinkhill.github.io/Codeigniter-gCharts/ GitHub Project Page
 * @license http://opensource.org/licenses/MIT MIT
 */

class sizeAxis extends configOptions
{
    /**
     * Maximum radius of the largest possible bubble, in pixels.
     *
     * @var int
     */
    public $maxSize;

    /**
     * The size value to be mapped to $this->maxSize
     *
     * @var int
     */
    public $maxValue;

    /**
     * Mininum radius of the smallest possible bubble, in pixels
     *
     * @var int
     */
    public $minSize;

    /**
     * The size value to be mapped to $this->minSize
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
     * @param array Associative array containing key => value pairs for the
     * various configuration options.
     * @return \sizeAxis
     */
    public function __construct($config = array())
    {
        $this->options = array(
            'maxSize',
            'maxValue',
            'minSize',
            'minValue'
        );

        parent::__construct($config);
    }

    /**
     * Sets maximum radius of the largest possible bubble, in pixels
     *
     * @param int $maxSize
     * @return \sizeAxis
     */
    function maxSize($maxSize)
    {
        if(is_numeric($maxSize))
        {
            $this->maxSize = $maxSize;
        } else {
            $this->type_error(__FUNCTION__, 'int | float');
        }

        return $this;
    }

    /**
     * Set the size value (as appears in the chart data) to be mapped to
     * $this->maxSize. Larger values will be cropped to this value.
     *
     * @param int $maxValue
     * @return \sizeAxis
     */
    function maxValue($maxValue)
    {
        if(is_numeric($maxValue))
        {
            $this->maxValue = $maxValue;
        } else {
            $this->type_error(__FUNCTION__, 'int | float');
        }

        return $this;
    }

    /**
     * Sets mininum radius of the smallest possible bubble, in pixels
     *
     * @param int $minSize
     * @return \sizeAxis
     */
    function minSize($minSize)
    {
        if(is_numeric($minSize))
        {
            $this->minSize = $minSize;
        } else {
            $this->type_error(__FUNCTION__, 'int | float');
        }

        return $this;
    }

    /**
     * Set the size value (as appears in the chart data) to be mapped to
     * $this->minSize. Larger values will be cropped to this value.
     *
     * @param int $minValue
     * @return \sizeAxis
     */
    function minValue($minValue)
    {
        if(is_numeric($minValue))
        {
            $this->minValue = $minValue;
        } else {
            $this->type_error(__FUNCTION__, 'int | float');
        }

        return $this;
    }


}
