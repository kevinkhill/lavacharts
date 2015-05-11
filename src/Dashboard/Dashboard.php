<?php

namespace Khill\Lavacharts\Dashboard;

use \Khill\Lavacharts\Dashboard\Binding;

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
     * Arry of Binding objects, mapping controls to charts.
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
        $this->bindings[] = new Binding($controlWrap, $chartWrap);
    }
}
