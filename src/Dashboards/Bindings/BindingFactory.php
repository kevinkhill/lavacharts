<?php

namespace Khill\Lavacharts\Dashboards\Bindings;

use Khill\Lavacharts\Dashboards\Wrappers\ChartWrapper;
use Khill\Lavacharts\Dashboards\Wrappers\ControlWrapper;
use Khill\Lavacharts\Exceptions\BindingException;

/**
 * BindingFactory Class
 *
 * Creates new bindings for dashboards.
 *
 * @package   Khill\Lavacharts\Dashboards\Bindings
 * @since     3.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class BindingFactory
{
    /**
     * Create a new Binding for the Dashboard.
     *
     * @param  ControlWrapper|ControlWrapper[] $controlWraps One or array of many ControlWrappers
     * @param  ChartWrapper|ChartWrapper[]     $chartWraps   One or array of many ChartWrappers
     * @throws \Khill\Lavacharts\Exceptions\BindingException
     * @return Binding
     */
    public static function create($controlWraps, $chartWraps)
    {
        if ($controlWraps instanceof ControlWrapper && $chartWraps instanceof ChartWrapper) {
            return new OneToOne($controlWraps, $chartWraps);
        }

        if ($controlWraps instanceof ControlWrapper && is_array($chartWraps)) {
            return new OneToMany($controlWraps, $chartWraps);
        }

        if (is_array($controlWraps) && $chartWraps instanceof ChartWrapper) {
            return new ManyToOne($controlWraps, $chartWraps);
        }

        if (is_array($chartWraps) && is_array($controlWraps)) {
            return new ManyToMany($controlWraps, $chartWraps);
        }

        throw new BindingException;
    }
}
