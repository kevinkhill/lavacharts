<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\JsonConfig;
use \Khill\Lavacharts\Options;

/**
 * ChartArea ConfigObject
 *
 * An object containing all the values for the chartArea which can be
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
class ChartArea extends JsonConfig
{
    /**
     * Type of JsonConfig object
     *
     * @var string
     */
    const TYPE = 'ChartArea';

    /**
     * Default options for ChartArea
     *
     * @var array
     */
    private $defaults = [
        'left',
        'top',
        'width',
        'height'
    ];

    /**
     * Builds the ChartArea object when passed an array of configuration options.
     *
     * @param  array $config
     * @return \Khill\Lavacharts\Configs\ChartArea
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function __construct($config = [])
    {
        $options = new Options($this->defaults);

        parent::__construct($options, $config);
    }

    /**
     * Sets the left padding of the chart in the container.
     *
     * @param  int|string $left Amount in pixels as integer or percent
     * @return \Khill\Lavacharts\Configs\ChartArea
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function left($left)
    {
        return $this->setIntOrPercentOption(__FUNCTION__, $left);
    }

    /**
     * Sets the top padding of the chart in the container.
     *
     * @param  int|string $top Amount in pixels as integer or percent
     * @return \Khill\Lavacharts\Configs\ChartArea
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function top($top)
    {
        return $this->setIntOrPercentOption(__FUNCTION__, $top);
    }

    /**
     * Sets the width of the chart in the container.
     *
     * @param  int|string $width Amount in pixels as integer or percent
     * @return \Khill\Lavacharts\Configs\ChartArea
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function width($width)
    {
        return $this->setIntOrPercentOption(__FUNCTION__, $width);
    }

    /**
     * Sets the height of the chart in the container.
     *
     * @param  int|string $height Amount in pixels as integer or percent
     * @return \Khill\Lavacharts\Configs\ChartArea
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function height($height)
    {
        return $this->setIntOrPercentOption(__FUNCTION__, $height);
    }
}
