<?php namespace Khill\Lavacharts\Charts;
/**
 * AreaChart Class
 *
 * An area chart that is rendered within the browser using SVG or VML. Displays
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

class AreaChart extends Chart
{
    public function __construct($chartLabel)
    {
        parent::__construct($chartLabel);

        $this->defaults = array_merge($this->defaults, array(
//            'animation',
            'areaOpacity',
            'axisTitlesPosition',
            'events',
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
     * @return \AreaChart
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
     * @return \AreaChart
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
     * The default opacity of the colored area under an area chart series, where
     * 0.0 is fully transparent and 1.0 is fully opaque. To specify opacity for
     * an individual series, set the areaOpacity value in the series property.
     *
     * @param float $opacity
     * @return \AreaChart
     */
    public function areaOpacity($opacity)
    {
        if(is_float($opacity) && $opacity < 1.0 && $opacity > 0.0)
        {
            $this->addOption(array('areaOpacity' => $opacity));
        } else {
            $this->type_error(__FUNCTION__, 'float', 'between 0.0 - 1.0');
        }

        return $this;
    }

    /**
     * Where to place the axis titles, compared to the chart area. Supported values:
     *
     * in - Draw the axis titles inside the the chart area.
     * out - Draw the axis titles outside the chart area.
     * none - Omit the axis titles.
     *
     * @param string $position
     * @return \AreaChart
     */
    public function axisTitlesPosition($position)
    {
        $values = array('in', 'out', 'none');

        if(in_array($position, $values))
        {
            $this->addOption(array('axisTitlesPosition' => $position));
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
     * @return \AreaChart
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
     * @return \AreaChart
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
     * If set to true, series elements are stacked.
     *
     * @param type $isStacked
     * @return \AreaChart
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
     * Whether to guess the value of missing points. If true, it will guess the
     * value of any missing data based on neighboring points. If false, it will
     * leave a break in the line at the unknown point.
     *
     * @param boolean $interpolateNulls
     * @return \AreaChart
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
     * @return \AreaChart
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
     * @return \AreaChart
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
     * @return \AreaChart
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
