<?php

namespace Khill\Lavacharts\Laravel;

use Illuminate\Support\Facades\Facade;

/**
 * Lavacharts Facade
 *
 * Enables member methods via static accessor for Lavacharts in Laravel.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Laravel
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class LavachartsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'lavacharts';
    }
}
