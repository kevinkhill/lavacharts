<?php

use \Khill\Lavacharts\Charts\Chart;

class ControlWrapper {

    private $controlType;
    private $containerId;
    private $chart;

    public function __construct(Chart $chart)
    {
        $this->chart = $chart;
    }
}
