<?php

namespace Khill\Lavacharts\Dashboard\Bindings;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Dashboard\ChartWrapper;
use \Khill\Lavacharts\Dashboard\ControlWrapper;
use \Khill\Lavacharts\Exceptions\InvalidLabel;

/**
 * OneToOne Binding Class
 *
 * Binds a ControlWrapper to a ChartWrapper to use in dashboards.
 *
 * @package    Lavacharts
 * @subpackage Dashboard\Bindings
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class OneToOne extends Binding
{
    /**
     * ControlWrapper to bind to chart.
     *
     * @var \Khill\Lavacharts\Dashboard\ControlWrapper
     */
    private $controlWrapper;

    /**
     * ChartWrapper on which to bind a control.
     *
     * @var \Khill\Lavacharts\Dashboard\ChartWrapper
     */
    private $chartWrapper;

     /**
     * Binds a ControlWrapper to a ChartWrapper in the dashboard.
     *
     * @param  string $label Label for the binding
     * @param  \Khill\Lavacharts\Dashboard\ChartWrapper   $chartWrap
     * @param  \Khill\Lavacharts\Dashboard\ControlWrapper $controlWrap
     * @throws \Khill\Lavacharts\Exceptions\InvalidLabel  $label
     * @return self
     */
    public function __construct(ControlWrapper $controlWrapper, ChartWrapper $chartWrapper)
    {
        $this->chartWrapper   = $chartWrapper;
        $this->controlWrapper = $controlWrapper;
    }

    /**
     * Get the ChartWrapper
     *
     * @return \Khill\Lavacharts\Dashboard\ChartWrapper
     */
    public function getChartWrapper()
    {
        return $this->chartWrapper;
    }

    /**
     * Get the ControlWrapper
     *
     * @return \Khill\Lavacharts\Dashboard\ControlWrapper
     */
    public function getControlWrapper()
    {
        return $this->controlWrapper;
    }
}
