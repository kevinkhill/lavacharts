<?php namespace Khill\Lavacharts\Charts;
/**
 * Column Chart Class
 *
 * A vertical bar chart that is rendered within the browser using SVG or VML.
 * Displays tips when hovering over bars. For a horizontal version of this
 * chart, see the Bar Chart.
 *
 *
 * @author Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2013, KHill Designs
 * @link https://github.com/kevinkhill/Codeigniter-gCharts GitHub Repository Page
 * @link http://kevinkhill.github.io/Codeigniter-gCharts/ GitHub Project Page
 * @license http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Helpers\Helpers;

class ColumnChart extends Chart
{
    public function __construct($chartLabel)
    {
        parent::__construct($chartLabel);

        $this->defaults = array_merge($this->defaults, array(
//            'animation',
            'axisTitlesPosition',
            'barGroupWidth',
            'focusTarget',
            'hAxis',
            'isHtml',
//            'vAxes',
            'vAxis'
        ));
    }

    /**
     * Animation Easing
     *
     * The easing function applied to the animation. The following options are available:
     * 'linear' - Constant speed.
     * 'in' - Ease in - Start slow and speed up.
     * 'out' - Ease out - Start fast and slow down.
     * 'inAndOut' - Ease in and out - Start slow, speed up, then slow down.
     *
     * @param string $easing
     * @return \ColumnChart
     */
//    public function animationEasing($easing = 'linear')
//    {
//        $values = array('linear', 'in', 'out', 'inAndOut');
//
//        if(in_array($easing, $values))
//        {
//            $this->easing = $easing;
//            return $this;
//        } else {
//            $this->error('Invalid animationEasing value, must be (string) '.Helpers::array_string($values));
//        }
//
//        return $this;
//    }

    /**
     * Animation Duration
     *
     * The duration of the animation, in milliseconds.
     *
     * @param mixed $duration
     * @return \ColumnChart
     */
//    public function animationDuration($duration)
//    {
//        if(is_int($duration) || is_string($duration))
//        {
//            $this->duration = $this->_valid_int($duration);
//        } else {
//            $this->duration = 0;
//        }
//
//        return $this;
//    }

    /**
     * Where to place the axis titles, compared to the chart area. Supported values:
     * in - Draw the axis titles inside the the chart area.
     * out - Draw the axis titles outside the chart area.
     * none - Omit the axis titles.
     *
     * @param string $position
     * @return \ColumnChart
     */
    public function axisTitlesPosition($position)
    {
        $values = array(
            'in',
            'out',
            'none'
        );

        if(in_array($position, $values))
        {
            $this->addOption(array('axisTitlesPosition' => $position));
        } else {
            $this->type_error(__FUNCTION__, 'string', 'with a value of '.Helpers::array_string($values));
        }

        return $this;
    }

    /**
     * The width of a group of bars, specified in either of these formats:
     * - Pixels (e.g. 50).
     * - Percentage of the available width for each group (e.g. '20%'),
     *   where '100%' means that groups have no space between them.
     *
     * @param mixed $barGroupWidth
     * @return \ColumnChart
     */
    public function barGroupWidth($barGroupWidth)
    {
        if(Helpers::is_int_or_percent($barGroupWidth))
        {
//            $bar = new bar($barGroupWidth);
//            $this->addOption($bar->toArray());
            $this->addOption(array('bar' => array('groupWidth' => $barGroupWidth)));
        } else {
            $this->type_error(__FUNCTION__, 'string | int', 'must be a valid int or percent [ 50 | 65% ]');
        }

        return $this;
    }


    /**
     * An object with members to configure various horizontal axis elements. To
     * specify properties of this property, create a new hAxis() object, set
     * the values then pass it to this function or to the constructor.
     *
     * @param hAxis $hAxis
     * @return \ColumnChart
     */
    public function hAxis($hAxis)
    {
        if(Helpers::is_hAxis($hAxis))
        {
            $this->addOption($hAxis->toArray());
        } else {
            $this->type_error(__FUNCTION__, 'hAxis');
        }

        return $this;
    }

    /**
     * If set to true, use HTML-rendered (rather than SVG-rendered) tooltips.
     *
     * @todo was this merged into tooltip object???
     * @param boolean $isHTML
     * @return \ColumnChart
     */
    public function isHtml($isHTML)
    {
        if(is_bool($isHTML))
        {
            $this->addOption(array('isHTML' => $isHTML));
        } else {
            $this->error(__FUNCTION__, 'boolean');
        }

        return $this;
    }

    /**
     * If set to true, series elements are stacked.
     *
     * @param boolean $isStacked
     * @return \ColumnChart
     */
    public function isStacked($isStacked)
    {
        if(is_bool($isStacked))
        {
            $this->addOption(array('isStacked' => $isStacked));
        } else {
            $this->type_error(__FUNCTION__, 'boolean');
        }

        return $this;
    }

    /**
     * An object with members to configure various vertical axis elements. To
     * specify properties of this property, create a new vAxis() object, set
     * the values then pass it to this function or to the constructor.
     *
     * @param vAxis $vAxis
     * @return \ColumnChart
     */
    public function vAxis($vAxis)
    {
        if(Helpers::is_vAxis($vAxis))
        {
            $this->addOption($vAxis->toArray());
        } else {
            $this->type_error(__FUNCTION__, 'vAxis');
        }

        return $this;
    }

}
