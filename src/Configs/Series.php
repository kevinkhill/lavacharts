<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\JsonConfig;
use \Khill\Lavacharts\Options;
use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * Series ConfigObject
 *
 * An object containing all the values for a single series in a multiple data
 * set chart, which can be passed into the series property of the chart's options.
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
class Series extends JsonConfig
{
    /**
     * Type of JsonConfig object
     *
     * @var string
     */
    const TYPE = 'Series';

    /**
     * Default options for Series
     *
     * @var array
     */
    private $defaults = [
        'annotation',
        'areaOpacity',
        'curveType',
        'targetAxisIndex',
        'textStyle',
        'type'
    ];

    /**
     * Builds the series object when passed an array of configuration options.
     *
     * @param  array $config Options for the series
     * @return \Khill\Lavacharts\Configs\Series
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function __construct($config = [])
    {
        $options = new Options($this->defaults);

        parent::__construct($options, $config);
    }

    /**
     * An object to be applied to annotations for this series.
     *
     * This can be used to control, for instance, the textStyle for the series.
     *
     * @param  array $annotationConfig Style of the series annotations
     * @return \Khill\Lavacharts\Configs\Series
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function annotation($annotationConfig)
    {
        return $this->setOption(__FUNCTION__, new Annotation($annotationConfig));
    }

    /**
     * Opacity, with 0.0 being fully transparent and 1.0 fully opaque.
     *
     * @param  float $areaOpacity
     * @return \Khill\Lavacharts\Configs\Color
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function areaOpacity($areaOpacity)
    {
        if (Utils::between(0.0, $areaOpacity, 1.0, true) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'float',
                'between 0.0 - 1.0'
            );
        }

        return $this->setOption(__FUNCTION__, $areaOpacity);
    }

    /**
     * The color to use for this series.
     *
     * @param  string $color Valid HTML color string.
     * @return \Khill\Lavacharts\Configs\Series
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function color($color)
    {
        return $this->setStringOption(__FUNCTION__, $color);
    }

    /**
     * Controls the curve of the lines when the line width is not zero.
     *
     * Can be one of the following:
     * 'none'     - Straight lines without curve.
     * 'function' - The angles of the line will be smoothed.
     *
     * @param  string $curveType
     * @return \Khill\Lavacharts\Configs\Series
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function curveType($curveType)
    {
        $values = [
            'none',
            'function'
        ];

        return $this->setStringInArrayOption(__FUNCTION__, $curveType, $values);
    }

    /**
     * Overrides for the global Candlestick falling color options.
     *
     * @param  array $fallingColorConfig
     * @return \Khill\Lavacharts\Configs\Series
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function fallingColor($fallingColorConfig)
    {
        return $this->setOption(__FUNCTION__, new BackgroundColor($fallingColorConfig));
    }

    public function labelInLegend($labelInLegend)
    {
        //TODO: what is this?
    }

    public function lineDashStyle($lineDashStyle)
    {
        //TODO: what is this?
    }

    /**
     * Overrides the global lineWidth value for this series.
     *
     * Use zero to hide all lines and show only the points.
     * You can override values for individual series using the series property.
     *
     * @param  int $width
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function lineWidth($width)
    {
        return $this->sestIntOption(__FUNCTION__, $width);
    }

    /**
     * Overrides the global pointShape value for this series.
     *
     * Available values are:
     * 'circle', 'triangle', 'square', 'diamond', 'star', or 'polygon'.
     *
     * @see    https://developers.google.com/chart/interactive/docs/points
     * @param  string $shape Accepted values [circle|triangle|square|diamond|star|polygon]
     * @return \Khill\Lavacharts\Configs\Series
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function pointShape($shape)
    {
        $values = [
            'circle',
            'triangle',
            'square',
            'diamond',
            'star',
            'polygon'
        ];

        return $this->setStringInArrayOption(__FUNCTION__, $shape, $values);
    }

    /**
     * Overrides the global pointSize value for this series.
     *
     * Use zero to hide all points. You can override values for individual
     * series using the series property.
     *
     * @param  int $size
     * @return \Khill\Lavacharts\Configs\Series
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function pointSize($size)
    {
        return  $this->setIntOption(__FUNCTION__, $size);
    }

    /**
     * Overrides for the global Candlestick color options.
     *
     * @param  array $risingColorConfig
     * @return \Khill\Lavacharts\Configs\Series
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function risingColor($risingColorConfig)
    {
        return $this->setOption(__FUNCTION__, new BackgroundColor($risingColorConfig));
    }
    /**
     * Which axis to assign this series to.
     *
     * 0 is the default axis, and 1 is the opposite axis.
     *
     * Default value is 0; set to 1 to define a chart where different series
     * are rendered against different axes.
     *
     * At least one series much be allocated to the default axis.
     * You can define a different scale for different axes.
     *
     * @param  int $index
     * @return \Khill\Lavacharts\Configs\Series
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function targetAxisIndex($index)
    {
        return $this->setIntOption(__FUNCTION__, $index);
    }

    /**
     * The default line type for any series not specified in the series property.
     *
     * Available values are:
     * 'line', 'area', 'bars', 'candlesticks' and 'steppedArea'
     *
     * @param  string $type
     * @return \Khill\Lavacharts\Configs\Series
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function type($type)
    {
        $values = [
            'line',
            'area',
            'bars',
            'candlesticks',
            'steppedArea'
        ];

        return $this->setStringInArrayOption(__FUNCTION__, $type, $values);
    }

    /**
     * An object that specifies the series text style.
     *
     * @deprecated
     * @param  array $textStyleConfig
     * @return \Khill\Lavacharts\Configs\Series
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function textStyle($textStyleConfig)
    {
        return $this->addOption(__FUNCTION__, new TextStyle($textStyleConfig));
    }

    /**
     * Whether the series should have a legend entry or not.
     *
     * @param bool $visibleInLegend
     * @return \Khill\Lavacharts\Configs\Series
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function visibleInLegend($visibleInLegend)
    {
        return $this->setBoolOption(__FUNCTION__, $visibleInLegend);
    }
}
