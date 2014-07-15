<?php namespace Khill\Lavacharts\Configs;

/**
 * ChartArea Properties Object
 *
 * An object containing all the values for the chartArea which can be
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

use Khill\Lavacharts\Helpers\Helpers;
use Khill\Lavacharts\Exceptions\InvalidConfigValue;

class ChartArea extends ConfigOptions
{
    /**
     * @var int|string How far to draw the chart from the left border.
     */
    public $left = null;

    /**
     * @var int|string How far to draw the chart from the top border.
     */
    public $top = null;

    /**
     * @var int|string Width of the chart.
     */
    public $width = null;

    /**
     * @var int|string Height of the chart.
     */
    public $height = null;


    /**
     * Builds the chartArea object when passed an array of configuration options.
     *
     * @param array $config
     *
     * @return Khill\Lavacharts\Configs\ChartArea
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
     *
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return Khill\Lavacharts\Configs\ChartArea
     */
    public function left($left)
    {
        if (Helpers::isIntOrPercent($left)) {
            $this->left = $left;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'int | string',
                'representing pixels or a percent.'
            );
        }

        return $this;
    }

    /**
     * Sets the top padding of the chart in the container.
     *
     * @param int Amount in pixels
     *
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return Khill\Lavacharts\Configs\ChartArea
     */
    public function top($top)
    {
        if (Helpers::isIntOrPercent($top)) {
            $this->top = $top;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'int | string',
                'representing pixels or a percent.'
            );
        }

        return $this;
    }

    /**
     * Sets the width of the chart in the container.
     *
     * @param int Amount in pixels
     *
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return Khill\Lavacharts\Configs\ChartArea
     */
    public function width($width)
    {
        if (Helpers::isIntOrPercent($width)) {
            $this->width = $width;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'int | string',
                'representing pixels or a percent.'
            );
        }

        return $this;
    }

    /**
     * Sets the height of the chart in the container.
     *
     * @param int Amount in pixels
     *
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return Khill\Lavacharts\Configs\ChartArea
     */
    public function height($height)
    {
        if (Helpers::isIntOrPercent($height)) {
            $this->height = $height;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'int | string',
                'representing pixels or a percent.'
            );
        }

        return $this;
    }
}
