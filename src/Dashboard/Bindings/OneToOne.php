<?php

namespace Khill\Lavacharts\Dashboard\Bindings;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Dashboard\ChartWrapper;
use \Khill\Lavacharts\Dashboard\ControlWrapper;
use \Khill\Lavacharts\Exceptions\InvalidLabel;

/**
 * Binding Class
 *
 * Binds a singel ControlWrapper to a single ChartWrapper for use in dashboards.
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
class OneToOne extends Binding
{
    /**
     * Creates the new Binding.
     *
     * @param  string $label Label for the binding
     * @param  \Khill\Lavacharts\Dashboard\ChartWrapper   $chartWrap
     * @param  \Khill\Lavacharts\Dashboard\ControlWrapper $controlWrap
     * @throws \Khill\Lavacharts\Exceptions\InvalidLabel  $label
     * @return self
     */
    public function __construct(ControlWrapper $controlWrapper, ChartWrapper $chartWrapper)
    {
        $this->chartWrappers   = [$chartWrapper];
        $this->controlWrappers = [$controlWrapper];
    }
}
