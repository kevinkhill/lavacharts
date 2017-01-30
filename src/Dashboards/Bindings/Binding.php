<?php

namespace Khill\Lavacharts\Dashboards\Bindings;

/**
 * Parent Binding Class
 *
 * Binds a ControlWrapper to a ChartWrapper to use in dashboards.
 *
 * @package   Khill\Lavacharts\Dashboards\Bindings
 * @since     3.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
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
    public function __construct(array $controlWrappers, array $chartWrappers)
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
     * Get the a specific ChartWrap
     *
     * @since  3.1.0
     * @param  int $index Which chart wrap to retrieve
     * @return array
     */
    public function getChartWrap($index)
    {
        return $this->chartWrappers[$index];
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

    /**
     * Get the a specific ControlWrap
     *
     * @since  3.1.0
     * @param  int $index Which control wrap to retrieve
     * @return array
     */
    public function getControlWrap($index)
    {
        return $this->controlWrappers[$index];
    }
}
