<?php

namespace Khill\Lavacharts\Dashboards\Bindings;

/**
 * Parent Binding Class
 *
 * Binds a ControlWrapper to a ChartWrapper to use in dashboards.
 *
 * @package    Khill\Lavacharts
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
     * Array of ControlWrappers.
     *
     * @var array
     */
    protected $controlWrappers;

    /**
     * Array of ChartWrappers.
     *
     * @var array
     */
    protected $chartWrappers;

    /**
     * Assigns the wrappers and creates the new Binding.
     *
     * @param array $chartWrappers
     * @param array $controlWrappers
     */
    public function __construct($controlWrappers, $chartWrappers)
    {
        $this->chartWrappers   = $chartWrappers;
        $this->controlWrappers = $controlWrappers;
    }

    /**
     * Get the ChartWrappers
     *
     * @return array
     */
    public function getChartWrappers()
    {
        return $this->chartWrappers;
    }

    /**
     * Get the ControlWrappers
     *
     * @return array
     */
    public function getControlWrappers()
    {
        return $this->controlWrappers;
    }
}
