<?php

namespace Khill\Lavacharts\Dashboard;

use \Khill\Lavacharts\Dashboard\ChartWrapper;
use \Khill\Lavacharts\Dashboard\ControlWrapper;

/**
 * Binding Class
 *
 * Binds a control wrapper to chart wrapper to use in dashboards.
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
