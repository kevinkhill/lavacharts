<?php

namespace Khill\Lavacharts\Dashboard;

use \Khill\Lavacharts\Dashboard\ChartWrapper;
use \Khill\Lavacharts\Dashboard\ControlWrapper;

class Binding
{
    /**
     * ControlWrapper to bind to chart.
     *
     * @var \Khill\Lavacharts\Dashboard\ControlWrapper
     */
    public $controlWrapper;

    /**
     * ChartWrapper on which to bind a control.
     *
     * @var \Khill\Lavacharts\Dashboard\ChartWrapper
     */
    public $chartWrapper;

     /**
     * Binds a ControlWrapper to a ChartWrapper in the dashboard.
     *
     * @param  \Khill\Lavacharts\Dashboard\ChartWrapper   $chartWrap
     * @param  \Khill\Lavacharts\Dashboard\ControlWrapper $controlWrap
     * @return self
     */
    public function __construct(ControlWrapper $controlWrap, ChartWrapper $chartWrap)
    {
        $this->chartWrapper   = $chartWrap;
        $this->controlWrapper = $controlWrap;
    }
}
