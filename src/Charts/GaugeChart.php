<?php namespace Khill\Lavacharts\Charts;

/**
 * GaugeChart Class
 *
 * A gauge with a dial, rendered within the browser using SVG or VML.
 *
 *
 * @package    Lavacharts
 * @subpackage Charts
 * @since      v2.2.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Utils;

class GaugeChart extends Chart
{
    public $type = 'GaugeChart';

    public function __construct($chartLabel)
    {
        parent::__construct($chartLabel);

        $this->defaults = array(
            'datatable',
            'height',
            'width',

            'forceIFrame',
            'greenColor',
            'greenFrom',
            'greenTo',
            'majorTicks',
            'max',
            'min',
            'minorTicks',
            'redColor',
            'redFrom',
            'redTo',
            'yellowColor',
            'yellowFrom',
            'yellowTo'
        );
    }

    /**
     * Draws the chart inside an inline frame.
     * Note that on IE8, this option is ignored; all IE8 charts are drawn in i-frames.
     *
     * @param bool $iframe
     *
     * @return GaugeChart
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
    }

    /**
     * The color to use for the green section, in HTML color notation.
     *
     * @param  string $c
     *
     * @return GaugeChart
     */
    public function greenColor($c)
    {
        if (Utils::nonEmptyString($c)) {
            return $this->addOption(array(__FUNCTION__ => $c));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }
    }

    /**
     * The lowest value for a range marked by a green color.
     *
     * @param  int $gf
     *
     * @return GaugeChart
     */
    public function greenFrom($gf)
    {
        if (is_int($gf)) {
            return $this->addOption(array(__FUNCTION__ => $gf));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }
    }

    /**
     * The highest value for a range marked by a green color.
     *
     * @param  int $gt
     *
     * @return GaugeChart
     */
    public function greenTo($gt)
    {
        if (is_int($gt)) {
            return $this->addOption(array(__FUNCTION__ => $gt));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }
    }

    /**
     * Labels for major tick marks. The number of labels define the number of major ticks in all gauges.
     * The default is five major ticks, with the labels of the minimal and maximal gauge value.
     *
     * @param  array $mt
     *
     * @return GaugeChart
     */
    public function majorTicks($mt)
    {
        if (is_array($mt)) {
            return $this->addOption(array(__FUNCTION__ => $mt));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'array'
            );
        }
    }

    /**
     * The maximal value of a gauge.
     *
     * @param  int $m
     *
     * @return GaugeChart
     */
    public function max($m)
    {
        if (is_int($m)) {
            return $this->addOption(array(__FUNCTION__ => $m));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }
    }

    /**
     * The minimal value of a gauge.
     *
     * @param  int $m
     *
     * @return GaugeChart
     */
    public function min($m)
    {
        if (is_int($m)) {
            return $this->addOption(array(__FUNCTION__ => $m));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }
    }

    /**
     * The number of minor tick section in each major tick section.
     *
     * @param  int $mt
     *
     * @return GaugeChart
     */
    public function minorTicks($mt)
    {
        if (is_int($mt)) {
            return $this->addOption(array(__FUNCTION__ => $mt));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }
    }

    /**
     * The color to use for the red section, in HTML color notation.
     *
     * @param  string $c
     *
     * @return GaugeChart
     */
    public function redColor($c)
    {
        if (Utils::nonEmptyString($c)) {
            return $this->addOption(array(__FUNCTION__ => $c));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }
    }

    /**
     * The lowest value for a range marked by a red color.
     *
     * @param  int $rf
     *
     * @return GaugeChart
     */
    public function redFrom($rf)
    {
        if (is_int($rf)) {
            return $this->addOption(array(__FUNCTION__ => $rf));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }
    }

    /**
     * The highest value for a range marked by a red color.
     *
     * @param  int $rt
     *
     * @return GaugeChart
     */
    public function redTo($rt)
    {
        if (is_int($rt)) {
            return $this->addOption(array(__FUNCTION__ => $rt));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }
    }

    /**
     * The color to use for the yellow section, in HTML color notation.
     *
     * @param  string $c
     *
     * @return GaugeChart
     */
    public function yellowColor($c)
    {
        if (Utils::nonEmptyString($c)) {
            return $this->addOption(array(__FUNCTION__ => $c));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }
    }

    /**
     * The lowest value for a range marked by a yellow color.
     *
     * @param  int $yf
     *
     * @return GaugeChart
     */
    public function yellowFrom($yf)
    {
        if (is_int($yf)) {
            return $this->addOption(array(__FUNCTION__ => $yf));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }
    }

    /**
     * The highest value for a range marked by a yellow color.
     *
     * @param  int $yt
     *
     * @return GaugeChart
     */
    public function yellowTo($yt)
    {
        if (is_int($yt)) {
            return $this->addOption(array(__FUNCTION__ => $yt));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }
    }
}
