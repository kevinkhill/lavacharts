<?php

namespace \Khill\Lavacharts\Dashboard;

use \Khill\Lavacharts\Charts\Chart;

class ControlWrapper {

    /**
     * Javascript chart class.
     *
     * @var string
     */
    const VIZ_CLASS = 'google.visualization.ControlWrapper';

    private $controlType;
    private $containerId;
    private $chart;

    public function __construct(Chart $chart)
    {
        $this->chart = $chart;
    }
}
