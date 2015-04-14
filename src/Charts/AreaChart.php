<?php namespace Khill\Lavacharts\Charts;

/**
 * AreaChart Class
 *
 * An area chart that is rendered within the browser using SVG or VML. Displays
 * tips when hovering over points.
 *
 *
 * @package    Lavacharts
 * @subpackage Charts
 * @since      v1.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Utils;
use Khill\Lavacharts\Configs\HorizontalAxis;
use Khill\Lavacharts\Configs\VerticalAxis;

class AreaChart extends Chart
{
    public $type = 'AreaChart';

    public function __construct($chartLabel)
    {
        parent::__construct($chartLabel);

        $this->defaults = array_merge(
            $this->defaults,
            array(
                //'animation',
                'areaOpacity',
                'axisTitlesPosition',
                'focusTarget',
                'hAxis',
                'isHtml',
                'interpolateNulls',
                'lineWidth',
                'pointSize',
                //'vAxes',
                'vAxis'
            )
        );
    }

    /**
     * The default opacity of the colored area under an area chart series, where
     * 0.0 is fully transparent and 1.0 is fully opaque. To specify opacity for
     * an individual series, set the areaOpacity value in the series property.
     *
     * @param  float              $opacity
     * @throws InvalidConfigValue
     * @return AreaChart
     */
    public function areaOpacity($opacity)
    {
        if (Utils::between(0.0, $opacity, 1.0, true)) {
            $this->addOption(array('areaOpacity' => $opacity));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'float',
                'where 0 < opacity < 1'
            );
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
     * @param  string             $position
     * @throws InvalidConfigValue
     * @return AreaChart
     */
    public function axisTitlesPosition($position)
    {
        $values = array(
            'in',
            'out',
            'none'
        );

        if (is_string($position) && in_array($position, $values)) {
            $this->addOption(array('axisTitlesPosition' => $position));
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
     * @since  v2.4.1
     * @param  string     $ft
     * @return AreaChart
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
     * An object with members to configure various horizontal axis elements. To
     * specify properties of this property, create a new hAxis() object, set
     * the values then pass it to this function or to the constructor.
     *
     * @param  Lavacharts\Configs\HorizontalAxis $hAxis
     * @throws InvalidConfigValue
     * @return AreaChart
     */
    public function hAxis(HorizontalAxis $hAxis)
    {
        $this->addOption($hAxis->toArray('hAxis'));

        return $this;
    }

    /**
     * If set to true, series elements are stacked.
     *
     * @param  bool               $isStacked
     * @throws InvalidConfigValue
     * @return AreaChart
     */
    public function isStacked($isStacked)
    {
        if (is_bool($isStacked)) {
            $this->addOption(array('isStacked' => $isStacked));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'bool'
            );
        }

        return $this;
    }

    /**
     * Whether to guess the value of missing points. If true, it will guess the
     * value of any missing data based on neighboring points. If false, it will
     * leave a break in the line at the unknown point.
     *
     * @param  bool               $interpolateNulls
     * @throws InvalidConfigValue
     * @return AreaChart
     */
    public function interpolateNulls($interpolateNulls)
    {
        if (is_bool($interpolateNulls)) {
            $this->addOption(array('interpolateNulls' => $interpolateNulls));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'bool'
            );
        }

        return $this;
    }

    /**
     * Data line width in pixels. Use zero to hide all lines and show only the
     * points. You can override values for individual series using the series
     * property.
     *
     * @param  int                $width
     * @throws InvalidConfigValue
     * @return AreaChart
     */
    public function lineWidth($width)
    {
        if (is_int($width)) {
            $this->addOption(array('lineWidth' => $width));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return $this;
    }

    /**
     * Diameter of displayed points in pixels. Use zero to hide all points. You
     * can override values for individual series using the series property.
     *
     * @param  int                $size
     * @throws InvalidConfigValue
     * @return AreaChart
     */
    public function pointSize($size)
    {
        if (is_int($size)) {
            $this->addOption(array('pointSize' => $size));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return $this;
    }

    /**
     * An object with members to configure various vertical axis elements. To
     * specify properties of this property, create a new vAxis() object, set
     * the values then pass it to this function or to the constructor.
     *
     * @param  VerticalAxis       $vAxis
     * @throws InvalidConfigValue
     * @return AreaChart
     */
    public function vAxis(VerticalAxis $vAxis)
    {
        $this->addOption($vAxis->toArray('vAxis'));

        return $this;
    }
}
