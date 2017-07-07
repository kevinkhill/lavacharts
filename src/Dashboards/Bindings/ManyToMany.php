<?php

namespace Khill\Lavacharts\Dashboards\Bindings;

use Khill\Lavacharts\Dashboards\Wrappers\ChartWrapper;
use Khill\Lavacharts\Dashboards\Wrappers\ControlWrapper;

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
     * Creates the new Binding.
     *
     * @param  ControlWrapper[] $controlWrappers
     * @param  ChartWrapper[]   $chartWrappers
     */
    public function __construct(array $controlWrappers, array $chartWrappers)
    {
        parent::__construct($controlWrappers, $chartWrappers);
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return 'ManyToMany';
    }
}
