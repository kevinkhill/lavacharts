<?php namespace Khill\Lavacharts\Charts;

/**
 * Column Chart Class
 *
 * A vertical bar chart that is rendered within the browser using SVG or VML.
 * Displays tips when hovering over bars. For a horizontal version of this
 * chart, see the Bar Chart.
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

class ColumnChart extends Chart
{
    public $type = 'ColumnChart';

    public function __construct($chartLabel)
    {
        parent::__construct($chartLabel);

        $this->defaults = array_merge(
            $this->defaults,
            array(
                'axisTitlesPosition',
                'barGroupWidth',
                'focusTarget',
                'hAxis',
                'isHtml',
                'isStacked',
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
     * @param  string      $position
     * @return ColumnChart
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
     * The width of a group of bars, specified in either of these formats:
     * - Pixels (e.g. 50).
     * - Percentage of the available width for each group (e.g. '20%'),
     *   where '100%' means that groups have no space between them.
     *
     * @param  mixed       $barGroupWidth
     * @return ColumnChart
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
     * An object with members to configure various horizontal axis elements. To
     * specify properties of this property, create a new hAxis() object, set
     * the values then pass it to this function or to the constructor.
     *
     * @param  Lavacharts\Configs\HorizontalAxis $hAxis
     * @throws InvalidConfigValue
     * @return ColumnChart
     */
    public function hAxis(HorizontalAxis $hAxis)
    {
        $this->addOption($hAxis->toArray('hAxis'));

        return $this;
    }

    /**
     * If set to true or "relative", series elements are stacked.
     *
     * @param  mixed        $isStacked      bool or string "relative"
     * @return ColumnChart
     */
    public function isStacked($isStacked)
    {
        if (is_bool($isStacked)) {
            $this->addOption(array('isStacked' => $isStacked));
        } elseif (is_string($isStacked) && (strtolower($isStacked) == "relative")) {
            $this->addOption(array('isStacked' => strtolower($isStacked)));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string | bool',
                'must be bool or string with a value of relative'
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
     * @param  array              $a Array of VerticalAxis objects
     * @throws InvalidConfigValue
     *
     * @return ColumnChart
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
     * @param  Lavacharts\Configs\VerticalAxis $vAxis
     * @throws InvalidConfigValue
     * @return ColumnChart
     */
    public function vAxis(VerticalAxis $vAxis)
    {
        $this->addOption($vAxis->toArray('vAxis'));

        return $this;
    }
}
