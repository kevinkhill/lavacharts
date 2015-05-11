<?php

namespace Khill\Lavacharts\Dashboard;

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
     * Binds a ControlWrapper to a ChartWrapper in the dashboard.
     *
     * @param  \Khill\Lavacharts\Dashboard\ChartWrapper   $chartWrap
     * @param  \Khill\Lavacharts\Dashboard\ControlWrapper $controlWrap
     * @return self
     */
    public function bind(ControlWrapper $controlWrap, ChartWrapper $chartWrap)
    {
        $this->bindings[] = [
            'control' => $controlWrap,
            'chart'   => $chartWrap
        ];
    }
}
