<?php namespace Khill\Lavacharts\Charts;
/**
 * LineChart Class
 *
 * A line chart that is rendered within the browser using SVG or VML. Displays
 * tips when hovering over points.
 *
 *
 * @author Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2013, KHill Designs
 * @link https://github.com/kevinkhill/Codeigniter-gCharts GitHub Repository Page
 * @link http://kevinkhill.github.io/Codeigniter-gCharts/ GitHub Project Page
 * @license http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Helpers\Helpers;

class LineChart extends Chart
{
    public function __construct($chartLabel)
    {
        parent::__construct($chartLabel);

        $this->defaults = array_merge($this->defaults, array(
//            'animation',
            'axisTitlesPosition',
            'curveType',
            'hAxis',
            'isHtml',
            'interpolateNulls',
            'lineWidth',
            'pointSize',
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
     * @return \LineChart
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
     * @return \LineChart
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
     * @return \LineChart
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
     * Controls the curve of the lines when the line width is not zero. Can be one of the following:
     *
     * 'none' - Straight lines without curve.
     * 'function' - The angles of the line will be smoothed.
     *
     * @param string $curveType
     * @return \LineChart
     */
    public function curveType($curveType)
    {
        $values = array(
            'none',
            'function'
        );

        if(in_array($curveType, $values))
        {
            $this->addOption(array('curveType' => (string) $curveType));
        } else {
            $this->type_error(__FUNCTION__, 'string', 'with a value of '.Helpers::array_string($values));
        }

        return $this;
    }

    /**
     * An object with members to configure various horizontal axis elements. To
     * specify properties of this property, create a new hAxis() object, set
     * the values then pass it to this function or to the constructor.
     *
     * @param hAxis $hAxis
     * @return \LineChart
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
     * @param boolean $isHTML
     * @return \LineChart
     */
    public function isHtml($isHTML)
    {
        if(is_bool($isHTML))
        {
            $this->addOption(array('isHTML' => $isHTML));
        } else {
            $this->type_error(__FUNCTION__, 'boolean');
        }

        return $this;
    }

    /**
     * Whether to guess the value of missing points. If true, it will guess the
     * value of any missing data based on neighboring points. If false, it will
     * leave a break in the line at the unknown point.
     *
     * @param boolean $interpolateNulls
     * @return \LineChart
     */
    public function interpolateNulls($interpolateNulls)
    {
        if(is_bool($interpolateNulls))
        {
            $this->addOption(array('interpolateNulls' => $interpolateNulls));
        } else {
           $this->type_error(__FUNCTION__, 'boolean');
        }

        return $this;
    }

    /**
     * Data line width in pixels. Use zero to hide all lines and show only the
     * points. You can override values for individual series using the series
     * property.
     *
     * @param int $width
     * @return \LineChart
     */
    public function lineWidth($width)
    {
        if(is_int($width))
        {
            $this->addOption(array('lineWidth' => $width));
        } else {
            $this->type_error(__FUNCTION__, 'int');
        }

        return $this;
    }

    /**
     * Diameter of displayed points in pixels. Use zero to hide all points. You
     * can override values for individual series using the series property.
     *
     * @param int $size
     * @return \LineChart
     */
    public function pointSize($size)
    {
        if(is_int($size))
        {
            $this->addOption(array('pointSize' => $size));
        } else {
            $this->type_error(__FUNCTION__, 'int');
        }

        return $this;
    }

    /**
     * An object with members to configure various vertical axis elements. To
     * specify properties of this property, create a new vAxis() object, set
     * the values then pass it to this function or to the constructor.
     *
     * @param vAxis $vAxis
     * @return \LineChart
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
