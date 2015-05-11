<?php

namespace \Khill\Lavacharts\Dashboard;

use \Khill\Lavacharts\Charts\Chart;

class ChartWrapper {

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
     * Builds a ControlWrapper object.
     *
     * @param  \Khill\Lavacharts\Charts\Chart $chart
     * @param  \Khill\Lavacharts\Dashboard\Filters\Filter $filter
     * @param  string $containerId
     * @return self
     */
    public function __construct(Chart $chart, $containerId)
    {
        if (Utils::nonEmptyString($containerId) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        $this->chart       = $chart;
        $this->containerId = $containerId;
    }
}
