<?php namespace Khill\Lavacharts\Configs;

/**
 * Series Properties Object
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

use Khill\Lavacharts\Utils;
use Khill\Lavacharts\Exceptions\InvalidConfigValue;

class Series extends ConfigObject
{
    /**
     * Alignment of the series.
     *
     * @var Annotation
     */
    public $annotation;

    /**
     * Overrides the global curveType value for this series.
     *
     * @var string
     */
    public $curveType;

    /**
     * Which axis is the target of the series definition.
     *
     * @var string
     */
    public $targetAxisIndex;

    /**
     * Text style of the series.
     *
     * @var TextStyle
     */
    public $textStyle;

    /**
     * The type of marker for this series.
     *
     * @var string
     */
    public $type;


    /**
     * Builds the series object when passed an array of configuration options.
     *
     * @param  array  $config Options for the series
     * @return Series
     */
    public function __construct($config = array())
    {
        parent::__construct($this, $config);
    }

    /**
     * An object to be applied to annotations for this series.
     * This can be used to control, for instance, the textStyle for the series.
     *
     * @param  Annotation Style of the series annotations
     * @throws InvalidConfigValue
     * @return Series
     */
    public function annotation(Annotation $annotation)
    {
        if (Utils::isAnnotation($annotation)) {
            $this->annotation = $annotation;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'annotation'
            );
        }

        return $this;
    }


    /**
     * Controls the curve of the lines when the line width is not zero.
     *
     * Can be one of the following:
     * 'none'     - Straight lines without curve.
     * 'function' - The angles of the line will be smoothed.
     *
     * @param  string             $curveType
     * @throws InvalidConfigValue
     * @return Series
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
                __FUNCTION__,
                'string',
                'with a value of '.Utils::arrayToPipedString($values)
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
     * @param  int                $index
     * @throws InvalidConfigValue
     * @return Series
     */
    public function targetAxisIndex($index)
    {
        if (is_int($index)) {
            $this->targetAxisIndex = $index;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return $this;
    }

    /**
     * The default line type for any series not specified in the series property.
     * Available values are:
     * 'line', 'area', 'bars', 'candlesticks' and 'steppedArea'
     *
     * @param  string             $type
     * @throws InvalidConfigValue
     * @return Series
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
                __FUNCTION__,
                'string',
                'with a value of '.Utils::arrayToPipedString($values)
            );
        }

        return $this;
    }

    /**
     * An object that specifies the series text style.
     *
     * @param  TextStyle          $textStyle
     * @throws InvalidConfigValue
     * @return Series
     */
    public function textStyle(TextStyle $textStyle)
    {
        $this->textStyle = $textStyle;

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
[ ] visibleInLegend - A bool value, where true means that the series should have a legend entry, and false means that it should not. Default is true.

*/
