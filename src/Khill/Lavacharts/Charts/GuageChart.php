<?php namespace Khill\Lavacharts\Charts;

/**
 * GuageChart Class
 *
 * A calendar chart is a visualization used to show activity over the course of a long span of time,
 * such as months or years. They're best used when you want to illustrate how some quantity varies
 * depending on the day of the week, or how it trends over time.
 *
 *
 * @package    Lavacharts
 * @subpackage Charts
 * @since      v2.1.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Utils;

class GuageChart extends Chart
{
    public $type = 'GuageChart';

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
     * @return GuageChart
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
     *
     *
     * @param  string $c
     *
     * @return GuageChart
     */
    public function greenColor($c)
    {
        if (is_int($c)) {
            return $this->addOption(array(__FUNCTION__ => $c));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }
    }

    /**
     *
     *
     * @param  int $
     *
     * @return GuageChart
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
     *
     *
     * @param  int $gt
     *
     * @return GuageChart
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
     *
     *
     * @param  array $mt
     *
     * @return GuageChart
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
     *
     *
     * @param  int $m
     *
     * @return GuageChart
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
     *
     *
     * @param  int $m
     *
     * @return GuageChart
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
     *
     *
     * @param  int $mt
     *
     * @return GuageChart
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
     *
     *
     * @param  string $c
     *
     * @return GuageChart
     */
    public function redColor($c)
    {
        if (is_int($c)) {
            return $this->addOption(array(__FUNCTION__ => $c));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }
    }

    /**
     *
     *
     * @param  int $rf
     *
     * @return GuageChart
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
     *
     *
     * @param  int $rt
     *
     * @return GuageChart
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
     *
     *
     * @param  string $c
     *
     * @return GuageChart
     */
    public function yellowColor($c)
    {
        if (is_int($c)) {
            return $this->addOption(array(__FUNCTION__ => $c));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }
    }

    /**
     *
     *
     * @param  int $yf
     *
     * @return GuageChart
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
     *
     *
     * @param  int $yt
     *
     * @return GuageChart
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
