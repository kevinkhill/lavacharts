<?php

namespace Khill\Lavacharts\Dashboard;

use \Khill\Lavacharts\Utils;


use \Khill\Lavacharts\Exceptions\InvalidLabel;

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
     * Label for the binding.
     *
     * @var string
     */
    private $label;

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
    public function __construct($label, ControlWrapper $controlWrapper, ChartWrapper $chartWrapper)
    {
        if (Utils::nonEmptyString($label) === false) {
            throw new InvalidLabel($label);
        }

        $this->label          = $label;
        $this->chartWrapper   = $chartWrapper;
        $this->controlWrapper = $controlWrapper;
    }

    /**
     * Get the chart label.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
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
