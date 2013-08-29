<?php namespace Khill\Lavacharts\Configs;
/**
 * Chart Area Properties Object
 *
 * An object containing all the values for the chartArea which can be
 * passed into the chart's options.
 *
 *
 * @author Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2013, KHill Designs
 * @link https://github.com/kevinkhill/Codeigniter-gCharts GitHub Repository Page
 * @link http://kevinkhill.github.io/Codeigniter-gCharts/ GitHub Project Page
 * @license http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Helpers\Helpers;

class chartArea extends configOptions
{
    /**
     * How far to draw the chart from the left border.
     *
     * @var int Amount in pixels
     */
    public $left = NULL;

    /**
     * How far to draw the chart from the top border.
     *
     * @var int Amount in pixels
     */
    public $top = NULL;

    /**
     * Width of the chart.
     *
     * @var int Amount in pixels
     */
    public $width = NULL;

    /**
     * Height of the chart.
     *
     * @var int Amount in pixels
     */
    public $height = NULL;


    /**
     * Builds the chartArea object when passed an array of configuration options.
     *
     * @param array $config
     * @return \chartArea
     */
    public function __construct($config = array())
    {
        $this->options = array(
            'left',
            'top',
            'width',
            'height'
        );

        parent::__construct($config);
    }

    /**
     * Sets the left padding of the chart in the container.
     *
     * @param int Amount in pixels
     * @return \chartArea
     */
    public function left($left)
    {
        if(Helpers::is_int_or_percent($left))
        {
            $this->left = $left;
        } else {
            $this->type_error(__FUNCTION__, 'int | string', 'representing pixels or a percent.');
        }

        return $this;
    }

    /**
     * Sets the top padding of the chart in the container.
     *
     * @param int Amount in pixels
     * @return \chartArea
     */
    public function top($top)
    {
        if(Helpers::is_int_or_percent($top))
        {
            $this->top = $top;
        } else {
            $this->type_error(__FUNCTION__, 'int | string', 'representing pixels or a percent.');
        }

        return $this;
    }

    /**
     * Sets the width of the chart in the container.
     *
     * @param int Amount in pixels
     * @return \chartArea
     */
    public function width($width)
    {
        if(Helpers::is_int_or_percent($width))
        {
            $this->width = $width;
        } else {
            $this->type_error(__FUNCTION__, 'int | string', 'representing pixels or a percent.');
        }

        return $this;
    }

    /**
     * Sets the height of the chart in the container.
     *
     * @param int Amount in pixels
     * @return \chartArea
     */
    public function height($height)
    {
        if(Helpers::is_int_or_percent($height))
        {
            $this->height = $height;
        } else {
            $this->type_error(__FUNCTION__, 'int | string', 'representing pixels or a percent.');
        }

        return $this;
    }

}
