<?php namespace Khill\Lavacharts\Charts;

/**
 * Combo Chart Class
 *
 * A chart that lets you render each series as a different marker type from the following list:
 * line, area, bars, candlesticks and stepped area.
 *
 * To assign a default marker type for series, specify the seriesType property.
 * Use the series property to specify properties of each series individually.
 *
 *
 * @package    Lavacharts
 * @subpackage Charts
 * @since      v2.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Utils;
use Khill\Lavacharts\Configs\Annotation;
use Khill\Lavacharts\Configs\HorizontalAxis;
use Khill\Lavacharts\Configs\VerticalAxis;
use Khill\Lavacharts\Exceptions\InvalidConfigValue;

class ComboChart extends Chart
{
    //use Khill\Lavacharts\Traits\AxisTitlesPosition;

    public $type = 'ComboChart';

    public function __construct($c)
    {
        parent::__construct($c);

        $this->defaults = array_merge(
            $this->defaults,
            array(
                'axisTitlesPosition',
                'barGroupWidth',
                'focusTarget',
                'hAxis',
                'isHtml',
                'series',
                'seriesType',
                'vAxes',
                'vAxis'
            )
        );
    }

    /**
     * Where to place the axis titles, compared to the chart area. Supported values:
     * in - Draw the axis titles inside the the chart area.
     * out - Draw the axis titles outside the chart area.
     * none - Omit the axis titles.
     *
     * @param  Annotation $a
     * @throws InvalidConfigValue
     * @return ComboChart
     */
    public function annotations(Annotation $a)
    {
        return $this->addOption($a->toArray());
    }

    /**
     * The default opacity of the colored area under an area chart series, where
     * 0.0 is fully transparent and 1.0 is fully opaque. To specify opacity for
     * an individual series, set the areaOpacity value in the series property.
     *
     * @param  float              $o
     * @throws InvalidConfigValue
     * @return ComboChart
     */
    public function areaOpacity($o)
    {
        if (Utils::between(0.0, $o, 1.0)) {
            return $this->addOption(array(__FUNCTION__ => $o));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'float',
                'between 0.0 - 1.0'
            );
        }
    }

    /**
     * Where to place the axis titles, compared to the chart area. Supported values:
     * in   - Draw the axis titles inside the the chart area.
     * out  - Draw the axis titles outside the chart area.
     * none - Omit the axis titles.
     *
     * @param  string             $p
     * @throws InvalidConfigValue
     * @return ComboChart
     */
    public function axisTitlesPosition($p)
    {
        $v = array(
            'in',
            'out',
            'none'
        );

        if (in_array($p, $v)) {
            return $this->addOption(array(__FUNCTION__ => $p));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string',
                'with a value of '.Utils::arrayToPipedString($v)
            );
        }
    }

    /**
     * The width of a group of bars, specified in either of these formats:
     * - Pixels (e.g. 50).
     * - Percentage of the available width for each group (e.g. '20%'),
     *   where '100%' means that groups have no space between them.
     *
     * @param  int|string         $b
     * @throws InvalidConfigValue
     * @return ComboChart
     */
    public function barGroupWidth($b)
    {
        if (Utils::isIntOrPercent($b)) {
            return $this->addOption(array(__FUNCTION__ => array('groupWidth' => $b)));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string | int',
                'must be a valid int or percent [ 50 | 65% ]'
            );
        }
    }

   /**
     * An object with members to configure various horizontal axis elements. To
     * specify properties of this property, create a new hAxis() object, set
     * the values then pass it to this function or to the constructor.
     *
     * @param HorizontalAxis $h
     *
     * @throws InvalidConfigValue
     * @return ComboChart
     */
    public function hAxis(HorizontalAxis $h)
    {
        return $this->addOption($h->toArray(__FUNCTION__));
    }

    /**
     * If set to true, series elements are stacked.
     *
     * @param  bool               $i
     * @throws InvalidConfigValue
     * @return ComboChart
     */
    public function isStacked($i)
    {
        if (is_bool($i)) {
            return $this->addOption(array(__FUNCTION__ => $i));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'bool'
            );
        }
    }

   /**
     * An array of objects, each describing the format of the corresponding series
     * in the chart. To use default values for a series, specify an null in the array.
     * If a series or a value is not specified, the global value will be used.
     *
     * @param  array              $a
     * @throws InvalidConfigValue
     * @return ComboChart
     */
    public function series($a)
    {
        if (Utils::arrayValuesCheck($a, 'class', 'Series')) {
            return $this->addOption(array(__FUNCTION__ => $a));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'array',
                'Series Objects'
            );
        }
    }

    /**
     * The default line type for any series not specified in the series property.
     * Available values are:
     * 'line', 'area', 'bars', 'candlesticks' and 'steppedArea'
     *
     * @param  string             $t
     * @throws InvalidConfigValue
     * @return ComboChart
     */
    public function seriesType($t)
    {
        $v = array(
            'line',
            'area',
            'bars',
            'candlesticks',
            'steppedArea'
        );

        if (in_array($t, $v)) {
            return $this->addOption(array(__FUNCTION__ => $t));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string',
                'with a value of '.Utils::arrayToPipedString($v)
            );
        }
    }

    /**
     * Specifies properties for individual vertical axes
     *
     * If the chart has multiple vertical axes. Each child object is a vAxis object,
     * and can contain all the properties supported by vAxis.
     * These property values override any global settings for the same property.
     *
     * To specify a chart with multiple vertical axes, first define a new axis using
     * series.targetAxisIndex, then configure the axis using vAxes.
     *
     * @param  array              $a Array of VerticalAxis objects
     * @throws InvalidConfigValue
     *
     * @return ComboChart
     */
    public function vAxes($a)
    {
        if (Utils::arrayValuesCheck($a, 'class', 'VerticalAxis')) {
            return $this->addOption(array(__FUNCTION__ => $a));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'array',
                'of VerticalAxis Objects'
            );
        }
    }

    /**
     * An object with members to configure various vertical axis elements. To
     * specify properties of this property, create a new vAxis() object, set
     * the values then pass it to this function or to the constructor.
     *
     * @param  VerticalAxis       $v
     * @throws InvalidConfigValue
     *
     * @return ComboChart
     */
    public function vAxis(VerticalAxis $v)
    {
        return $this->addOption($v->toArray(__FUNCTION__));
    }
}

/*
areaOpacity - number, 0.0–1.0 - 0.3 - [[The default opacity of the colored area under an area chart series, where
    0.0 is fully transparent and 1.0 is fully opaque. To specify opacity for an
    individual series, set the areaOpacity value in the series
    property.]]

candlestick.hollowIsRising - bool - false (will later be changed to true) - [[If true, rising candles will appear hollow and falling candles will appear solid, otherwise, the opposite.]]
candlestick.fallingColor.fill - string - auto (depends on the series color and hollowIsRising) - [[The fill color of falling candles, as an HTML color string.]]
candlestick.fallingColor.stroke - string - auto (the series color) - [[The stroke color of falling candles, as an HTML color string.]]
candlestick.fallingColor.strokeWidth - number - 2 - [[The stroke width of falling candles, as an HTML color string.]]
candlestick.risingColor.fill - string - auto (white or the series color, depending on hollowIsRising) - [[The fill color of rising candles, as an HTML color string.]]
candlestick.risingColor.stroke - string - auto (the series color or white, depending on hollowIsRising) - [[The stroke color of rising candles, as an HTML color string.]]
candlestick.risingColor.strokeWidth - number - 2 - [[The stroke width of rising candles, as an HTML color string.]]
crosshair - object - null - [[An object containing the crosshair properties for the chart.]]
crosshair.color - string - default - [[The crosshair color, expressed as either a color name (e.g., "blue") or an RGB value (e.g., "#adf").]]
crosshair.focused - object - default - [[An object containing the crosshair properties upon focus.Example: crosshair: { focused: { color: '#3bc', opacity: 0.8 } }]]
crosshair.opacity - number - 1.0 - [[The crosshair opacity, with 0.0 being fully transparent and 1.0 fully opaque.]]
crosshair.orientation - string - 'both' - [[The crosshair orientation, which can be 'vertical' for vertical hairs only, 'horizontal' for horizontal hairs only, or 'both' for traditional crosshairs.]]
crosshair.selected - object - default - [[An object containing the crosshair properties upon selection.Example: crosshair: { selected: { color: '#3bc', opacity: 0.8 } }]]
crosshair.trigger - string - 'both' - [[When to display crosshairs: on 'focus', 'selection', or 'both'.]]
curveType - string - 'none' - [[Controls the curve of the lines when the line width is not zero.
    Can be one of the following:

      'none' - Straight lines without curve.
      'function' - The angles of the line will be smoothed.
    ]]
dataOpacity - number - 1.0 - [[The transparency of data points, with 1.0 being completely opaque and 0.0 fully transparent. In scatter, histogram, bar, and column charts, this refers to the visible data: dots in the scatter chart and rectangles in the others. In charts where selecting data creates a dot, such as the line and area charts, this refers to the circles that appear upon hover or selection. The combo chart exhibits both behaviors, and this option has no effect on other charts. (To change the opacity of a trendline, see trendline opacity.)]]
enableInteractivity - bool - true - [[Whether the chart throws user-based events or reacts to user interaction. If false, the chart will not throw 'select' or other interaction-based events (but will throw ready or error events), and will not display hovertext or otherwise change depending on user input.]]
focusTarget - string - 'datum' - [[
    The type of the entity that receives focus on mouse hover. Also affects which entity is selected by mouse click, and which data table element is associated with events. Can be one of the following:

      'datum' - Focus on a single data point. Correlates to a cell in the data table.
      'category' - Focus on a grouping of all data points along the major axis. Correlates to a row in the data table.

    In focusTarget 'category' the tooltip displays all the category values. This may be useful for comparing values of different series.
  ]]
forceIFrame - bool - false - [[Draws the chart inside an inline frame. (Note that on IE8, this option is ignored; all IE8 charts are drawn in i-frames.)]]
 -  -  - [[]]
interpolateNulls - bool - false - [[Whether to guess the value of missing points. If true, it will guess the
    value of any missing data based on neighboring points. If false, it will
    leave a break in the line at the unknown point.]]
isStacked - bool - false - [[If set to true, series elements of the same type are stacked.
        Affects bar, column and area series only.]]
lineWidth - number - 2 - [[Data line width in pixels. Use zero to hide all lines and show only the
    points. You can override values for individual series using the
    series property.]]
orientation - string - 'horizontal' - [[The orientation of the chart. When set
  to 'vertical', rotates the axes of the chart so that
  (for instance) a column chart becomes a bar chart, and an area chart
  grows rightward instead of up:

  <p>[This section requires a browser that supports JavaScript and iframes.]</p>

]]
pointShape - string - 'circle' - [[The shape of individual data elements: 'circle', 'triangle', 'square', 'diamond', 'star', or 'polygon'. See the points documentation for examples.]]
pointSize - number - 0 - [[Diameter of displayed points in pixels. Use zero to hide all points.
     You can override values for individual series using the
    series property.]]
reverseCategories - bool - false - [[
    If set to true, will draw series from right to left. The default is to draw left-to-right.

      This option is only supported for a discrete major axis.

  ]]
selectionMode - string - 'single' - [[
    When selectionMode is 'multiple', users may select multiple data points.
  ]]
series - Array of objects, or object with nested objects - {} - [[An array of objects, each describing the format of the corresponding series in the chart. To use default values for a series, specify an empty object {}. If a series or a value is not specified, the global value will be used. Each object supports the following properties:

        annotations - An object to be applied to annotations for this series. This can be used to control, for instance, the textStyle for the series:series: {  0: {    annotations: {      textStyle: {fontSize: 12, color: 'red' }    }  }}See the various annotations options for a more complete list of what can be customized.
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
        visibleInLegend - A bool value, where true means that the series should have a legend entry, and false means that it should not. Default is true.

    You can specify either an array of objects, each of which applies to the
      series in the order given, or you can specify an object where each child
      has a numeric key indicating which series it applies to. For example, the
      following two declarations are identical, and declare the first series as
      black and absent from the legend, and the fourth as red and absent from the legend:
    series: [{color: 'black', visibleInLegend: false}, {}, {},
                      {color: 'red', visibleInLegend: false}]
series: {0:{color: 'black', visibleInLegend: false},
         3:{color: 'red', visibleInLegend: false}}
  ]]
seriesType - string - 'line' - [[The default line type for any series not specified in the series property. Available values are 'line', 'area', 'bars', 'candlesticks' and 'steppedArea'.]]
theme - string - null - [[A theme is a set of predefined option values that work together to achieve a specific chart behavior or visual effect. Currently only one theme is available:

      'maximized' - Maximizes the area of the chart, and draws the legend and all of the labels inside the chart area. Sets the following options:
        chartArea: {width: '100%', height: '100%'},
legend: {position: 'in'},
titlePosition: 'in', axisTitlesPosition: 'in',
hAxis: {textPosition: 'in'}, vAxis: {textPosition: 'in'}


  ]]
vAxes - Array of object, or object with child objects - null - [[Specifies properties for individual vertical axes, if the chart has
    multiple vertical axes. Each child object is a vAxis object,
    and can contain all the properties supported by vAxis. These
    property values override any global settings for the same property.
    To specify a chart with multiple vertical axes, first define a new axis
      using series.targetAxisIndex, then configure the axis using
      vAxes. The following example assigns series 2 to the right
      axis and specifies a custom title and text style for it:
    series:{2:{targetAxisIndex:1}}, vAxes:{1:{title:'Losses',textStyle:{color: 'red'}}}
    This property can be either an object or an array: the object is a
      collection of objects, each with a numeric label that specifies the axis
      that it defines--this is the format shown above; the array is an array of
      objects, one per axis. For example, the following array-style notation is
      identical to the vAxis object shown above:
    vAxes:[
{}, // Nothing specified for axis 0
{title:'Losses',textStyle:{color: 'red'}} // Axis 1
]]]

*/
