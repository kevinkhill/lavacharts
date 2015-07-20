<?php

namespace Khill\Lavacharts\Dashboards\Bindings;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Dashboards\ChartWrapper;
use \Khill\Lavacharts\Dashboards\ControlWrapper;
use \Khill\Lavacharts\Exceptions\InvalidBindings;

/**
 * BindingFactory Class
 *
 * Creates new bindings for dashboards.
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
class BindingFactory
{
    /**
     * Create a new Binding for the dashboard.
     *
     * @param  mixed $arg1 One or array of many ControlWrappers
     * @param  mixed $arg2 One or array of many ChartWrappers
     * @throws \Khill\Lavacharts\Exceptions\InvalidBindings
     * @return \Khill\Lavacharts\Dashboards\Binding
     */
    public static function create($arg1, $arg2)
    {
        if ($arg1 instanceof ControlWrapper && $arg2 instanceof ChartWrapper)
        {
            return new OneToOne($arg1, $arg2);
        }
        else if ($arg1 instanceof ControlWrapper && Utils::arrayValuesCheck($arg2, 'class', 'ChartWrapper'))
        {
            return new OneToMany($arg1, $arg2);
        }
        else if (Utils::arrayValuesCheck($arg1, 'class', 'ControlWrapper') && $arg2 instanceof ChartWrapper)
        {
            return new ManyToOne($arg1, $arg2);
        }
        else if (Utils::arrayValuesCheck($arg1, 'class', 'ControlWrapper') && Utils::arrayValuesCheck($arg2, 'class', 'ChartWrapper'))
        {
            return new ManyToMany($arg1, $arg2);
        }
        else
        {
            throw new InvalidBindings;
        }
    }
}
