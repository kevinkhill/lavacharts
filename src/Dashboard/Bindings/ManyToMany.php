<?php

namespace Khill\Lavacharts\Dashboard\Bindings;

/**
 * ManyToMany Binding Class
 *
 * Binds multiple ControlWrappers to a multiple ChartWrapper for use in dashboards.
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
class ManyToMany extends Binding
{
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
}
