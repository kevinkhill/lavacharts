<?php

namespace Khill\Lavacharts\Charts;

use \Khill\Lavacharts\Options;
use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\DataTables\DataTable;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * GaugeChart Class
 *
 * A gauge with a dial, rendered within the browser using SVG or VML.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Charts
 * @since      2.2.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class GaugeChart extends Chart
{
    /**
     * Common Methods
     */
    use \Khill\Lavacharts\Traits\ForceIFrameTrait;

    /**
     * Javascript chart type.
     *
     * @var string
     */
    const TYPE = 'GaugeChart';

    /**
     * Javascript chart version.
     *
     * @var string
     */
    const VERSION = '1';

    /**
     * Javascript chart package.
     *
     * @var string
     */
    const VIZ_PACKAGE = 'gauge';

    /**
     * Google's visualization class name.
     *
     * @var string
     */
    const VIZ_CLASS = 'google.visualization.Gauge';

    /**
     * Default configuration options for the chart.
     *
     * @var array
     */
    private $gaugeDefaults = [
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

    /**
     * Builds a new GaugeChart with the given label, datatable and options.
     *
     * @param \Khill\Lavacharts\Values\Label         $chartLabel Identifying label for the chart.
     * @param \Khill\Lavacharts\DataTables\DataTable $datatable DataTable used for the chart.
     * @param array                                  $config
     */
    public function __construct(Label $chartLabel, DataTable $datatable, $config = [])
    {
        $options = new Options($this->gaugeDefaults);

        parent::__construct($chartLabel, $datatable, $options, $config);
    }

    /**
     * The color to use for the green section, in HTML color notation.
     *
     * @param  string $greenColor
     * @return \Khill\Lavacharts\Charts\GaugeChart
     */
    public function greenColor($greenColor)
    {
        return $this->setStringOption(__FUNCTION__, $greenColor);
    }

    /**
     * The lowest value for a range marked by a green color.
     *
     * @param  integer $greenFrom
     * @return \Khill\Lavacharts\Charts\GaugeChart
     */
    public function greenFrom($greenFrom)
    {
        return $this->setIntOption(__FUNCTION__, $greenFrom);
    }

    /**
     * The highest value for a range marked by a green color.
     *
     * @param  integer $greenTo
     * @return \Khill\Lavacharts\Charts\GaugeChart
     */
    public function greenTo($greenTo)
    {
        return $this->setIntOption(__FUNCTION__, $greenTo);
    }

    /**
     * Labels for major tick marks. The number of labels define the number of major ticks in all gauges.
     * The default is five major ticks, with the labels of the minimal and maximal gauge value.
     *
     * @param  array $majorTicks
     * @return \Khill\Lavacharts\Charts\GaugeChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function majorTicks($majorTicks)
    {
        if (is_array($majorTicks) === false) {
            throw new InvalidConfigValue(
                static::TYPE . '->' . __FUNCTION__,
                'array'
            );
        }

        return $this->setOption(__FUNCTION__, $majorTicks);
    }

    /**
     * The maximal value of a gauge.
     *
     * @param  integer $max
     * @return \Khill\Lavacharts\Charts\GaugeChart
     */
    public function max($max)
    {
        return $this->setIntOption(__FUNCTION__, $max);
    }

    /**
     * The minimal value of a gauge.
     *
     * @param  integer $min
     * @return \Khill\Lavacharts\Charts\GaugeChart
     */
    public function min($min)
    {
        return $this->setIntOption(__FUNCTION__, $min);
    }

    /**
     * The number of minor tick section in each major tick section.
     *
     * @param  integer $minorTicks
     * @return \Khill\Lavacharts\Charts\GaugeChart
     */
    public function minorTicks($minorTicks)
    {
        return $this->setIntOption(__FUNCTION__, $minorTicks);
    }

    /**
     * The color to use for the red section, in HTML color notation.
     *
     * @param  string $redColor
     * @return \Khill\Lavacharts\Charts\GaugeChart
     */
    public function redColor($redColor)
    {
        return $this->setStringOption(__FUNCTION__, $redColor);
    }

    /**
     * The lowest value for a range marked by a red color.
     *
     * @param  integer $redFrom
     * @return \Khill\Lavacharts\Charts\GaugeChart
     */
    public function redFrom($redFrom)
    {
        return $this->setIntOption(__FUNCTION__, $redFrom);
    }

    /**
     * The highest value for a range marked by a red color.
     *
     * @param  integer $redTo
     * @return \Khill\Lavacharts\Charts\GaugeChart
     */
    public function redTo($redTo)
    {
        return $this->setIntOption(__FUNCTION__, $redTo);
    }

    /**
     * The color to use for the yellow section, in HTML color notation.
     *
     * @param  string $yellowColor
     * @return \Khill\Lavacharts\Charts\GaugeChart
     */
    public function yellowColor($yellowColor)
    {
        return $this->setStringOption(__FUNCTION__, $yellowColor);
    }

    /**
     * The lowest value for a range marked by a yellow color.
     *
     * @param  integer $yellowFrom
     * @return \Khill\Lavacharts\Charts\GaugeChart
     */
    public function yellowFrom($yellowFrom)
    {
        return $this->setIntOption(__FUNCTION__, $yellowFrom);
    }

    /**
     * The highest value for a range marked by a yellow color.
     *
     * @param  integer $yellowTo
     * @return \Khill\Lavacharts\Charts\GaugeChart
     */
    public function yellowTo($yellowTo)
    {
        return $this->setIntOption(__FUNCTION__, $yellowTo);
    }
}
