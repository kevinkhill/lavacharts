<?php

namespace Khill\Lavacharts\Dashboards\Bindings;

/**
 * Parent Binding Class
 *
 * Binds a ControlWrapper to a ChartWrapper to use in dashboards.
 *
 * @package    Lavacharts
 * @subpackage Dashboards\Bindings
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
     * ControlWrappers to bind to ChartWrappers.
     *
     * @var array
     */
    protected $controlWrappers;

    /**
     * ChartWrappers with to bound ControlWrappers.
     *
     * @var array
     */
    protected $chartWrappers;

    /**
     * Creates the new Binding.
     *
     * @param  array $chartWrappers
     * @param  array $controlWrappers
     * @return self
     */
    public function __construct($controlWrappers, $chartWrappers)
    {
        $this->chartWrappers   = $chartWrappers;
        $this->controlWrappers = $controlWrappers;
    }

    /**
     * Get the ChartWrappers
     *
     * @return mixed
     */
    public function getChartWrappers()
    {
        return $this->chartWrappers;
    }

    /**
     * Get the ControlWrappers
     *
     * @return mixed
     */
    public function getControlWrappers()
    {
        return $this->controlWrappers;
    }
}
