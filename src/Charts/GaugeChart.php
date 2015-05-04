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

    private $extraOptions = [
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
    ];

    public function __construct($chartLabel)
    {
        parent::__construct($chartLabel, $this->extraOptions);
    }

    /**
     * Draws the chart inside an inline frame.
     * Note that on IE8, this option is ignored; all IE8 charts are drawn in i-frames.
     *
     * @param  bool $iframe
     * @return GaugeChart
     */
    public function forceIFrame($iframe)
    {
        if (is_bool($iframe) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'bool'
            );
        }

        return $this->addOption([__FUNCTION__ => $iframe]);
    }

    /**
     * The color to use for the green section, in HTML color notation.
     *
     * @param  string $greenColor
     * @return GaugeChart
     */
    public function greenColor($greenColor)
    {
        if (Utils::nonEmptyString($greenColor) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this->addOption([__FUNCTION__ => $greenColor]);
    }

    /**
     * The lowest value for a range marked by a green color.
     *
     * @param  int $greenFrom
     * @return GaugeChart
     */
    public function greenFrom($greenFrom)
    {
        if (is_int($greenFrom) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return $this->addOption([__FUNCTION__ => $greenFrom]);
    }

    /**
     * The highest value for a range marked by a green color.
     *
     * @param  int $greenTo
     * @return GaugeChart
     */
    public function greenTo($greenTo)
    {
        if (is_int($greenTo) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return $this->addOption([__FUNCTION__ => $greenTo]);
    }

    /**
     * Labels for major tick marks. The number of labels define the number of major ticks in all gauges.
     * The default is five major ticks, with the labels of the minimal and maximal gauge value.
     *
     * @param  array $majorTicks
     * @return GaugeChart
     */
    public function majorTicks($majorTicks)
    {
        if (is_array($majorTicks) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'array'
            );
        }

        return $this->addOption([__FUNCTION__ => $majorTicks]);
    }

    /**
     * The maximal value of a gauge.
     *
     * @param  int $max
     * @return GaugeChart
     */
    public function max($max)
    {
        if (is_int($max) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return $this->addOption([__FUNCTION__ => $max]);
    }

    /**
     * The minimal value of a gauge.
     *
     * @param  int $min
     * @return GaugeChart
     */
    public function min($min)
    {
        if (is_int($min) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return $this->addOption([__FUNCTION__ => $min]);
    }

    /**
     * The number of minor tick section in each major tick section.
     *
     * @param  int $minorTicks
     * @return GaugeChart
     */
    public function minorTicks($minorTicks)
    {
        if (is_int($minorTicks) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return $this->addOption([__FUNCTION__ => $minorTicks]);
    }

    /**
     * The color to use for the red section, in HTML color notation.
     *
     * @param  string $redColor
     * @return GaugeChart
     */
    public function redColor($redColor)
    {
        if (Utils::nonEmptyString($redColor) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this->addOption([__FUNCTION__ => $redColor]);
    }

    /**
     * The lowest value for a range marked by a red color.
     *
     * @param  int $redFrom
     * @return GaugeChart
     */
    public function redFrom($redFrom)
    {
        if (is_int($redFrom) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return $this->addOption([__FUNCTION__ => $redFrom]);
    }

    /**
     * The highest value for a range marked by a red color.
     *
     * @param  int $redTo
     * @return GaugeChart
     */
    public function redTo($redTo)
    {
        if (is_int($redTo) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return $this->addOption([__FUNCTION__ => $redTo]);
    }

    /**
     * The color to use for the yellow section, in HTML color notation.
     *
     * @param  string $yellowColor
     * @return GaugeChart
     */
    public function yellowColor($yellowColor)
    {
        if (Utils::nonEmptyString($yellowColor) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this->addOption([__FUNCTION__ => $yellowColor]);
    }

    /**
     * The lowest value for a range marked by a yellow color.
     *
     * @param  int $yellowFrom
     * @return GaugeChart
     */
    public function yellowFrom($yellowFrom)
    {
        if (is_int($yellowFrom) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return $this->addOption([__FUNCTION__ => $yellowFrom]);
    }

    /**
     * The highest value for a range marked by a yellow color.
     *
     * @param  int $yellowTo
     * @return GaugeChart
     */
    public function yellowTo($yellowTo)
    {
        if (is_int($yellowTo) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return $this->addOption([__FUNCTION__ => $yellowTo]);
    }
}
