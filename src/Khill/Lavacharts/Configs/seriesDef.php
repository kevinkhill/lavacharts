<?php namespace Khill\Lavacharts\Configs;
/**
 * seriesDef Properties Object
 *
 * An object containing all the values for a single series in a multiple data
 * set chart, which can be passed into the series property of the chart's options.
 *
 *
 * @author Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2014, KHill Designs
 * @link https://github.com/kevinkhill/LavaCharts GitHub Repository Page
 * @link http://kevinkhill.github.io/LavaCharts/ GitHub Project Page
 * @license http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Helpers\Helpers;

class seriesDef extends configOptions
{
    /**
     * Position of the series.
     *
     * @var string
     */
    public $position;

    /**
     * Alignment of the series.
     *
     * @var string
     */
    public $alignment;

    /**
     * Text style of the series.
     *
     * @var textStyle
     */
    public $textStyle;

    /**
     * Builds the series object when passed an array of configuration options.
     *
     * @param array Options for the series
     * @return \tooltip
     */
    public function __construct($config = array())
    {
        $this->options = array(
            'position',
            'alignment',
            'textStyle'
        );

        parent::__construct($config);
    }

    /**
     * Sets the position of the series.
     *
     * Can be one of the following:
     * 'right'  - To the right of the chart. Incompatible with the vAxes option.
     * 'top'    - Above the chart.
     * 'bottom' - Below the chart.
     * 'in'     - Inside the chart, by the top left corner.
     * 'none'   - No series is displayed.
     *
     * @param string Location of series
     * @return \series
     */
    public function position($position)
    {
        $values = array(
            'right',
            'top',
            'bottom',
            'in',
            'none'
        );

        if(in_array($position, $values))
        {
            $this->position = $position;
        } else {
            $this->type_error(__FUNCTION__, 'string', 'with a value of '.Helpers::array_string($values));
        }

        return $this;
    }

    /**
     * Sets the alignment of the series.
     *
     * Can be one of the following:
     * 'start'  - Aligned to the start of the area allocated for the series.
     * 'center' - Centered in the area allocated for the series.
     * 'end'    - Aligned to the end of the area allocated for the series.
     *
     * Start, center, and end are relative to the style -- vertical or horizontal -- of the series.
     * For example, in a 'right' series, 'start' and 'end' are at the top and bottom, respectively;
     * for a 'top' series, 'start' and 'end' would be at the left and right of the area, respectively.
     *
     * The default value depends on the series's position. For 'bottom' seriess,
     * the default is 'center'; other seriess default to 'start'.
     *
     * @param string Alignment of the series
     * @return \series
     */
    public function alignment($alignment)
    {
        $values = array(
            'start',
            'center',
            'end'
        );

        if(in_array($alignment, $values))
        {
            $this->alignment = $alignment;
        } else {
            $this->type_error(__FUNCTION__, 'string', 'with a value of '.Helpers::array_string($values));
        }

        return $this;
    }

    /**
     * An object that specifies the series text style.
     *
     * @param textStyle Style of the series
     * @return \series
     */
    public function textStyle($textStyle)
    {
        if(Helpers::is_textStyle($textStyle))
        {
            $this->textStyle = $textStyle->getValues();
        } else {
            $this->type_error(__FUNCTION__, 'textStyle');
        }

        return $this;
    }

}

/*

annotations - An object to be applied to annotations for this series. This can be used to control, for instance, the textStyle for the series:
series: {
  0: {
    annotations: {
      textStyle: {fontSize: 12, color: 'red' }
    }
  }
}
See the various annotations options for a more complete list of what can be customized.
areaOpacity - Overrides the global areaOpacity for this series.
color - The color to use for this series. Specify a valid HTML color string.
curveType - Overrides the global curveType value for this series.
fallingColor.fill - Overrides the global candlestick.fallingColor.fill value for this series.
fallingColor.stroke - Overrides the global candlestick.fallingColor.stroke value for this series.
fallingColor.strokeWidth - Overrides the global candlestick.fallingColor.strokeWidth value for this series.
lineWidth - Overrides the global lineWidth value for this series.
pointShape - Overrides the global pointShape value for this series.
pointSize - Overrides the global pointSize value for this series.
risingColor.fill - Overrides the global candlestick.risingColor.fill value for this series.
risingColor.stroke - Overrides the global candlestick.risingColor.stroke value for this series.
risingColor.strokeWidth - Overrides the global candlestick.risingColor.strokeWidth value for this series.
targetAxisIndex - Which axis to assign this series to, where 0 is the default axis, and 1 is the opposite axis. Default value is 0; set to 1 to define a chart where different series are rendered against different axes. At least one series much be allocated to the default axis. You can define a different scale for different axes.
type - The type of marker for this series. Valid values are 'line', 'area', 'bars', 'candlesticks' and 'steppedArea'. Note that bars are actually vertical bars (columns). The default value is specified by the chart's seriesType option.
visibleInLegend - A boolean value, where true means that the series should have a legend entry, and false means that it should not. Default is true.

*/
