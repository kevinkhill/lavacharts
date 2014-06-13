<?php namespace Khill\Lavacharts\Configs;

/**
 * Series Properties Object
 *
 * An object containing all the values for a single series in a multiple data
 * set chart, which can be passed into the series property of the chart's options.
 *
 *
 * @category  Class
 * @package   Khill\Lavacharts\Configs
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2014, KHill Designs
 * @link      https://github.com/kevinkhill/LavaCharts GitHub Repository Page
 * @link      http://kevinkhill.github.io/LavaCharts/ GitHub Project Page
 * @license   http://opensource.org/licenses/GPL-3.0 GPLv3
 */

use Khill\Lavacharts\Helpers\Helpers;
use Khill\Lavacharts\Exceptions\InvalidConfigValue;
use Khill\Lavacharts\Configs\Annotation;
use Khill\Lavacharts\Configs\TextStyle;

class Series extends ConfigOptions
{
    /**
     * Alignment of the series.
     *
     * @var Khill\Lavacharts\Configs\Annotation
     */
    public $annotation;

    /**
     * Overrides the global curveType value for this series.
     *
     * @var string
     */
    public $curveType;

    /**
     * The type of marker for this series.
     *
     * @var string
     */
    public $type;


    /**
     * Text style of the series.
     *
     * @var Khill\Lavacharts\Configs\TextStyle
     */
    public $textStyle;


    /**
     * Builds the series object when passed an array of configuration options.
     *
     * @param array Options for the series
     *
     * @return Khill\Lavacharts\Configs\Series
     */
    public function __construct($config = array())
    {
        $this->options = array(
            'annotation',
            'curveType',
            'targetAxisIndex',
            'type',
        );

        parent::__construct($config);
    }

    /**
     * An object to be applied to annotations for this series.
     * This can be used to control, for instance, the textStyle for the series.
     *
     * @param annotation Annotation style of the series
     *
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return Khill\Lavacharts\Configs\Series
     */
    public function annotation(Annotation $annotation)
    {
        if (Helpers::isAnnotation($annotation)) {
            $this->annotation = $annotation;
        } else {
            throw new InvalidConfigValue($this->className, __FUNCTION__, 'annotation');
        }

        return $this;
    }


    /**
     * Controls the curve of the lines when the line width is not zero.
     *
     * Can be one of the following:
     * 'none' - Straight lines without curve.
     * 'function' - The angles of the line will be smoothed.
     *
     * @param string $curveType
     *
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return Khill\Lavacharts\Charts\LineChart
     */
    public function curveType($curveType)
    {
        $values = array(
            'none',
            'function'
        );

        if (is_string($curveType) && in_array($curveType, $values)) {
            $this->curveType = $curveType;
        } else {
            throw new InvalidConfigValue(
                $this->chartType,
                __FUNCTION__,
                'string',
                'with a value of '.Helpers::arrayToPipedString($values)
            );
        }

        return $this;
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
     * @param int $index
     *
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return Khill\Lavacharts\Configs\Series
     */
    public function targetAxisIndex($index) {
        if (is_int($index)) {
            $this->targetAxisIndex = $index;
        } else {
            throw new InvalidConfigValue($this->className, __FUNCTION__, 'index');
        }

        return $this;
    }

    /**
     * The default line type for any series not specified in the series property.
     * Available values are:
     * 'line', 'area', 'bars', 'candlesticks' and 'steppedArea'
     *
     * @param string $type
     *
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return Khill\Lavacharts\Configs\Series
     */
    public function type($type)
    {
        $values = array(
            'line',
            'area',
            'bars',
            'candlesticks',
            'steppedArea'
        );

        if (in_array($type, $values)) {
            $this->type = $type;
        } else {
            throw new InvalidConfigValue(
                $this->className,
                __FUNCTION__,
                'string',
                'with a value of '.Helpers::arrayToPipedString($values)
            );
        }

        return $this;
    }

    /**
     * An object that specifies the series text style.
     *
     * @param Khill\Lavacharts\Configs\TextStyle $textStyle
     *
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return Khill\Lavacharts\Configs\Series
     */
    public function textStyle(TextStyle $textStyle)
    {
        if (Helpers::isTextStyle($textStyle)) {
            $this->textStyle = $textStyle;
        } else {
            throw new InvalidConfigValue($this->className, __FUNCTION__, 'textStyle');
        }

        return $this;
    }
}

/*

[x] annotations - :An object to be applied to annotations for this series. This can be used to control, for instance, the textStyle for the series
series: {
  0: {
    annotations: {
      textStyle: {fontSize: 12, color: 'red' }
    }
  }
}   See the various annotations options for a more complete list of what can be customized.

[ ] areaOpacity - Overrides the global areaOpacity for this series.
[ ] color - The color to use for this series. Specify a valid HTML color string.
[x] curveType - Overrides the global curveType value for this series.
[ ] fallingColor.fill - Overrides the global candlestick.fallingColor.fill value for this series.
[ ] fallingColor.stroke - Overrides the global candlestick.fallingColor.stroke value for this series.
[ ] fallingColor.strokeWidth - Overrides the global candlestick.fallingColor.strokeWidth value for this series.
[ ] lineWidth - Overrides the global lineWidth value for this series.
[ ] pointShape - Overrides the global pointShape value for this series.
[ ] pointSize - Overrides the global pointSize value for this series.
[ ] risingColor.fill - Overrides the global candlestick.risingColor.fill value for this series.
[ ] risingColor.stroke - Overrides the global candlestick.risingColor.stroke value for this series.
[ ] risingColor.strokeWidth - Overrides the global candlestick.risingColor.strokeWidth value for this series.
[x] targetAxisIndex - Which axis to assign this series to, where 0 is the default axis, and 1 is the opposite axis. Default value is 0; set to 1 to define a chart where different series are rendered against different axes. At least one series much be allocated to the default axis. You can define a different scale for different axes.
[x] type - The type of marker for this series. Valid values are 'line', 'area', 'bars', 'candlesticks' and 'steppedArea'. Note that bars are actually vertical bars (columns). The default value is specified by the chart's seriesType option.
[ ] visibleInLegend - A boolean value, where true means that the series should have a legend entry, and false means that it should not. Default is true.

*/
