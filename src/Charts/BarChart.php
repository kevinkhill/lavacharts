<?php namespace Khill\Lavacharts\Charts;

/**
 * BarChart Class
 *
 * A vertical bar chart that is rendered within the browser using SVG or VML.
 * Displays tips when hovering over bars. For a horizontal version of this
 * chart, see the Bar Chart.
 *
 *
 * @package    Lavacharts
 * @subpackage Charts
 * @since      v2.3.0
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

class BarChart extends Chart
{
    public $type = 'BarChart';

    public function __construct($chartLabel)
    {
        parent::__construct($chartLabel);

        $this->defaults = array_merge(
            $this->defaults,
            array(
                //'animation'
                'annotations',
                'axisTitlesPosition',
                'barGroupWidth',
                //'bars',
                //'chart.subtitle',
                //'chart.title',
                'dataOpacity',
                'enableInteractivity',
                'focusTarget',
                'forceIFrame',
                'hAxes',
                'hAxis',
                'isStacked',
                'reverseCategories',
                'orientation',
                'series',
                'theme',
                'trendlines',
                'trendlines.n.color',
                'trendlines.n.degree',
                'trendlines.n.labelInLegend',
                'trendlines.n.lineWidth',
                'trendlines.n.opacity',
                'trendlines.n.pointSize',
                'trendlines.n.showR2',
                'trendlines.n.type',
                'trendlines.n.visibleInLegend',
                'vAxis',
                'vAxis'
            )
        );
    }

    /**
     * Defines how chart annotations will be displayed.
     *
     * @param  Annotation $a
     * @return BarChart
     */
    public function annotations(Annotation $a)
    {
        $this->addOption($a->toArray(__FUNCTION__));

        return $this;
    }

    /**
     * Where to place the axis titles, compared to the chart area. Supported values:
     * in - Draw the axis titles inside the the chart area.
     * out - Draw the axis titles outside the chart area.
     * none - Omit the axis titles.
     *
     * @param  string   $position
     * @return BarChart
     */
    public function axisTitlesPosition($position)
    {
        $values = array(
            'in',
            'out',
            'none'
        );

        if (Utils::nonEmptyStringInArray($position, $values)) {
            $this->addOption(array(__FUNCTION__ => $position));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string',
                'with a value of '.Utils::arrayToPipedString($values)
            );
        }

        return $this;
    }

    /**
     * The width of a group of bars, specified in either of these formats:
     * - Pixels (e.g. 50).
     * - Percentage of the available width for each group (e.g. '20%'),
     *   where '100%' means that groups have no space between them.
     *
     * @param  mixed       $barGroupWidth
     * @return BarChart
     */
    public function barGroupWidth($barGroupWidth)
    {
        if (Utils::isIntOrPercent($barGroupWidth)) {
            $this->addOption(array('bar' => array('groupWidth' => $barGroupWidth)));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string | int',
                'must be a valid int or percent [ 50 | 65% ]'
            );
        }

        return $this;
    }

    /**
     * The transparency of data points, with 1.0 being completely opaque and 0.0 fully transparent.
     *
     * @param  float    $do
     * @return BarChart
     */
    public function dataOpacity($do)
    {
        if (Utils::between(0.0, $do, 1.0)) {
            $this->addOption(array(__FUNCTION__ => $do));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'float',
                'between 0.0 - 1.0'
            );
        }

        return $this;
    }

    /**
     * Whether the chart throws user-based events or reacts to user interaction.
     *
     * If false, the chart will not throw 'select' or other interaction-based events
     * (but will throw ready or error events), and will not display hovertext or
     * otherwise change depending on user input.
     *
     * @param  bool     $ei
     * @return BarChart
     */
    public function enableInteractivity($ei)
    {
        if (is_bool($ei)) {
            $this->addOption(array(__FUNCTION__ => $ei));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'bool'
            );
        }

        return $this;
    }

    /**
     * The type of the entity that receives focus on mouse hover.
     *
     * Also affects which entity is selected by mouse click, and which data table
     * element is associated with events. Can be one of the following:
     *  'datum'    - Focus on a single data point. Correlates to a cell in the data table.
     *  'category' - Focus on a grouping of all data points along the major axis.
     *               Correlates to a row in the data table.
     *
     * In focusTarget 'category' the tooltip displays all the category values.
     * This may be useful for comparing values of different series.
     *
     * @param  string     $ft
     * @return BarChart
     */
    public function focusTarget($ft)
    {
        $values = array(
            'datum',
            'category'
        );

        if (Utils::nonEmptyStringInArray($ft, $values)) {
            $this->addOption(array(__FUNCTION__ => $ft));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string',
                'must be one of '.Utils::arrayToPipedString($values)
            );
        }

        return $this;
    }

    /**
     * Draws the chart inside an inline frame.
     * Note that on IE8, this option is ignored; all IE8 charts are drawn in i-frames.
     *
     * @param bool $iframe
     *
     * @return BarChart
     */
    public function forceIFrame($iframe)
    {
        if (is_bool($iframe)) {
            $this->addOption(array(__FUNCTION__ => $iframe));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'bool'
            );
        }

        return $this;
    }

    /**
     * Specifies properties for individual horizontal axes, if the chart has multiple horizontal axes.
     *
     * Each child object is a hAxis object, and can contain all the properties supported by hAxis.
     * These property values override any global settings for the same property.
     *
     * To specify a chart with multiple horizontal axes, first define a new axis using series.targetAxisIndex,
     * then configure the axis using hAxes.
     *
     * @param  array              $arr Array of HorizontalAxis objects
     * @throws InvalidConfigValue
     *
     * @return ComboChart
     */
    public function hAxes($arr)
    {
        if (Utils::arrayValuesCheck($arr, 'class', 'HorizontalAxis')) {
            return $this->addOption(array(__FUNCTION__ => $arr));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'array',
                'of HorizontalAxis Objects'
            );
        }

        return $this;
    }

    /**
     * An object with members to configure various horizontal axis elements. To
     * specify properties of this property, create a new HorizontalAxis object, set
     * the values then pass it to this function or to the constructor.
     *
     * @param  HorizontalAxis     $ha
     * @throws InvalidConfigValue
     * @return BarChart
     */
    public function hAxis(HorizontalAxis $ha)
    {
        $this->addOption($ha->toArray(__FUNCTION__));

        return $this;
    }

    public function orientation($o)
    {
        $values = array(
            'horizontal',
            'vertical'
        );

        if (Utils::nonEmptyStringInArray($o, $values)) {
            $this->addOption(array(__FUNCTION__ => $o));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string',
                'must be one of '.Utils::arrayToPipedString($values)
            );
        }

        return $this;
    }

    /**
     * If set to true, series elements are stacked.
     *
     * @param  bool        $is
     * @return BarChart
     */
    public function isStacked($is)
    {
        if (is_bool($is)) {
            $this->addOption(array(__FUNCTION__ => $is));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'bool'
            );
        }

        return $this;
    }

    /**
     * If set to true, will draw series from bottom to top. The default is to draw top-to-bottom.
     *
     * @param  bool               $rc
     * @throws InvalidConfigValue
     * @return BarChart
     */
    public function reverseCategories($rc)
    {
        if (is_bool($rc)) {
            $this->addOption(array(__FUNCTION__ => $rc));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'bool'
            );
        }

        return $this;
    }

   /**
     * An array of objects, each describing the format of the corresponding series
     * in the chart. To use default values for a series, specify an null in the array.
     * If a series or a value is not specified, the global value will be used.
     *
     * @param  array              $arr
     * @throws InvalidConfigValue
     * @return BarChart
     */
    public function series($arr)
    {
        if (Utils::arrayValuesCheck($arr, 'class', 'Series')) {
            $this->addOption(array(__FUNCTION__ => $arr));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'array',
                'Series Objects'
            );
        }

        return $this;
    }

    /**
     * A theme is a set of predefined option values that work together to achieve a specific chart
     * behavior or visual effect. Currently only one theme is available:
     *  'maximized' - Maximizes the area of the chart, and draws the legend and all of the
     *                labels inside the chart area. Sets the following options:
     *
     * chartArea: {width: '100%', height: '100%'},
     * legend: {position: 'in'},
     * titlePosition: 'in', axisTitlesPosition: 'in',
     * hAxis: {textPosition: 'in'}, vAxis: {textPosition: 'in'}
     *
     * @param  string     $t
     * @return BarChart
     */
    public function theme($t)
    {
        $values = array(
            'maximized'
        );

        if (Utils::nonEmptyStringInArray($t, $values)) {
            $this->addOption(array(__FUNCTION__ => $t));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string',
                'must be one of '.Utils::arrayToPipedString($values)
            );
        }

        return $this;
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
     * @param  array              $arr Array of VerticalAxis objects
     * @throws InvalidConfigValue
     *
     * @return BarChart
     */
    public function vAxes($arr)
    {
        if (Utils::arrayValuesCheck($arr, 'class', 'VerticalAxis')) {
            $this->addOption(array(__FUNCTION__ => $arr));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'array',
                'of VerticalAxis Objects'
            );
        }

        return $this;
    }

    /**
     * An object with members to configure various vertical axis elements. To
     * specify properties of this property, create a new VerticalAxis object, set
     * the values then pass it to this function or to the constructor.
     *
     * @param  VerticalAxis       $va
     * @throws InvalidConfigValue
     * @return BarChart
     */
    public function vAxis(VerticalAxis $va)
    {
        $this->addOption($va->toArray(__FUNCTION__));

        return $this;
    }
}
