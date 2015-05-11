<?php

namespace \Khill\Lavacharts\Dashboard;

use \Khill\Lavacharts\Dashboard\ChartWrapper;
use \Khill\Lavacharts\Dashboard\ControlWrapper;

class Dashboard {

    /**
     * Javascript chart package.
     *
     * @var string
     */
    const VIZ_PACKAGE = 'controls';

    /**
     * Javascript chart class.
     *
     * @var string
     */
    const VIZ_CLASS = 'google.visualization.Dashboard';

    /**
     * Control to chart bindings.
     *
     * @var array
     */
    private $bindings = [];

    /**
     * Builds a new Dashboard.
     *
     * @param  string $label
     * @return self
     */
    public function __construct($label)
    {
        # code...
    }

    /**
     *
     * @param
     * @return self
     */
    public function bind(ControlWrapper $controlWrap, ChartWrapper $chartWrap)
    {
        # code...
    }
}
