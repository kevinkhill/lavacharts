<?php

namespace Khill\Lavacharts\Dashboard;

use \Khill\Lavacharts\Utils;
//use \Khill\Lavacharts\Dashboard\Wrapper;
use \Khill\Lavacharts\Charts\Chart;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * ChartWrapper Class
 *
 * Used for wrapping charts to use in dashboards.
 *
 * @package    Lavacharts
 * @subpackage Dashboard
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class ChartWrapper implements \JsonSerializable
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
     * ContainerId of the div to render the control into.
     *
     * @var string
     */
    private $containerId;

    /**
     * Builds a ChartWrapper object.
     *
     * @param  \Khill\Lavacharts\Charts\Chart $chart
     * @param  string $containerId
     * @return self
     */
    public function __construct(Chart $chart, $containerId)
    {
        if (Utils::nonEmptyString($containerId) === false) {
            throw new InvalidConfigValue(
                get_class(),
                __FUNCTION__,
                'string'
            );
        }

        $this->chart       = $chart;
        $this->type        = $chart::TYPE;
        $this->containerId = $containerId;
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
     * Returns the container id.
     *
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function getContainerId()
    {
        return $this->containerId;
    }

    /**
     * Return a serialized ChartWrapper.
     *
     * @return string JSON
     */
    public function toJson()
    {
        return json_encode($this);
    }

    /**
     * Custom serialization of the ChartWrapper.
     *
     * @return array
     */
    public function jsonSerialize() {
        return [
            'chartType'   => $this->type,
            'containerId' => $this->containerId,
            'options'     => $this->chart->getOptions()
        ];
    }
}
