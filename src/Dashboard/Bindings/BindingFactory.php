<?php

namespace Khill\Lavacharts\Dashboard\Bindings;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Dashboard\ChartWrapper;
use \Khill\Lavacharts\Dashboard\ControlWrapper;
use \Khill\Lavacharts\Exceptions\InvalidLabel;

/**
 * BindingFactory Class
 *
 * Creates new bindings for dashboards.
 *
 * @package    Lavacharts
 * @subpackage Dashboard\Bindings
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class BindingFactory
{
    public static function oneToOne(ControlWrapper $controlWrapper, ChartWrapper $chartWrapper)
    {
        return new OneToOne($controlWrapper, $chartWrapper);
    }

    public static function oneToMany(ControlWrapper $controlWrapper, $chartWrapperArray)
    {
        if (Utils::arrayValuesCheck($chartWrapperArray, 'class', 'ChartWrapper') === false) {
            throw new Exception;
        }

        return new OneToMany($controlWrapper, $chartWrapperArray);
    }

    public static function manyToOne($controlWrapperArray, ChartWrapper $chartWrapper)
    {
        if (Utils::arrayValuesCheck($chartWrapperArray, 'class', 'ControlWrapper') === false) {
            throw new Exception;
        }

        return new ManyToOne($controlWrapperArray, $chartWrapper);
    }
}
