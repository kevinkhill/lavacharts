<?php

namespace Khill\Lavacharts\Dashboards;

use \Khill\Lavacharts\Values\ElementId;;
use \Khill\Lavacharts\Charts\Chart;

/**
 * ChartWrapper Class
 *
 * Used for wrapping charts to use in dashboards.
 *
 * @package    Lavacharts
 * @subpackage Dashboards
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class ChartWrapper extends Wrapper
{
    /**
     * Javascript chart class.
     *
     * @var string
     */
    const VIZ_CLASS = 'google.visualization.ChartWrapper';

    /**
     * Chart object to be wrapped.
     *
     * @var \Khill\Lavacharts\Charts\Chart
     */
    private $chart;

    /**
     * Builds a ChartWrapper object.
     *
     * @param  \Khill\Lavacharts\Charts\Chart $chart
     * @param  \Khill\Lavacharts\Values\ElementId $containerId
     * @return self
     */
    public function __construct(Chart $chart, ElementId $containerId)
    {
        parent::__construct($containerId);

        $this->chart = $chart;
        $this->type  = $chart::TYPE;
    }

    /**
     * Returns the wrapped chart.
     *
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function getChart()
    {
        return $this->chart;
    }

    /**
     * Custom serialization of the ChartWrapper.
     *
     * @return array
     */
    public function jsonSerialize() {
        return [
            'chartType'   => $this->type,
            'containerId' => (string) $this->containerId,
            'options'     => $this->chart->getOptions()
        ];
    }
}
