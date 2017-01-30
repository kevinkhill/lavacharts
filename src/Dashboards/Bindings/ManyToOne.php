<?php

namespace Khill\Lavacharts\Dashboards\Bindings;

use Khill\Lavacharts\Dashboards\Wrappers\ChartWrapper;

/**
 * ManyToOne Binding Class
 *
 * Binds multiple ControlWrappers to a single ChartWrapper for use in dashboards.
 *
 * @package   Khill\Lavacharts\Dashboards\Bindings
 * @since     3.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class ManyToOne extends Binding
{
    /**
     * Type of binding.
     *
     * @var string
     */
    const TYPE = 'ManyToOne';

    /**
     * Creates the new Binding.
     *
     * @param  array                                              $controlWrappers
     * @param  \Khill\Lavacharts\Dashboards\Wrappers\ChartWrapper $chartWrapper
     */
    public function __construct(array $controlWrappers, ChartWrapper $chartWrapper)
    {
        parent::__construct($controlWrappers, [$chartWrapper]);
    }
}
