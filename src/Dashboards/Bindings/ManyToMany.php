<?php

namespace Khill\Lavacharts\Dashboards\Bindings;

/**
 * ManyToMany Binding Class
 *
 * Binds multiple ControlWrappers to a multiple ChartWrapper for use in dashboards.
 *
 * @package   Khill\Lavacharts\Dashboards\Bindings
 * @since     3.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class ManyToMany extends Binding
{
    /**
     * Type of binding.
     *
     * @var string
     */
    const TYPE = 'ManyToMany';

    /**
     * Creates the new Binding.
     *
     * @param  array $chartWrappers
     * @param  array $controlWrappers
     */
    public function __construct(array $controlWrappers, array $chartWrappers)
    {
        parent::__construct($controlWrappers, $chartWrappers);
    }
}
