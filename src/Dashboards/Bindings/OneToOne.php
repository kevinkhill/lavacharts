<?php

namespace Khill\Lavacharts\Dashboards\Bindings;

use \Khill\Lavacharts\Dashboards\ChartWrapper;
use \Khill\Lavacharts\Dashboards\ControlWrapper;

/**
 * Binding Class
 *
 * Binds a single ControlWrapper to a single ChartWrapper for use in dashboards.
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
class OneToOne extends Binding
{
    /**
     * Type of binding.
     *
     * @var string
     */
    const TYPE = 'OneToOne';

    /**
     * Creates the new Binding.
     *
     * @param  \Khill\Lavacharts\Dashboards\ChartWrapper   $chartWrap
     * @param  \Khill\Lavacharts\Dashboards\ControlWrapper $controlWrap
     * @return self
     */
    public function __construct(ControlWrapper $controlWrapper, ChartWrapper $chartWrapper)
    {
        parent::__construct([$controlWrapper], [$chartWrapper]);
    }
}
