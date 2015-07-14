<?php

namespace Khill\Lavacharts\Dashboard\Bindings;

use \Khill\Lavacharts\Dashboard\ControlWrapper;

/**
 * OneToMany Binding Class
 *
 * Binds a single ControlWrapper to multiple ChartWrappers for use in dashboards.
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
class OneToMany extends Binding
{
    /**
     * Creates the new Binding.
     *
     * @param  \Khill\Lavacharts\Dashboard\ControlWrapper $controlWrap
     * @param  array $chartWrappers
     * @return self
     */
    public function __construct(ControlWrapper $controlWrapper, $chartWrappers)
    {
        $this->chartWrappers   = $chartWrappers;
        $this->controlWrappers = $controlWrapper;
    }
}
