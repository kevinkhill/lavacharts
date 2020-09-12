<?php

namespace Khill\Lavacharts\Laravel;

use Illuminate\Support\Facades\Facade;

/**
 * Lavacharts Facade
 *
 * Enables member methods via static accessor for Lavacharts in Laravel.
 *
 *
 * @package    Khill\Lavacharts\Laravel
 * @since      2.5.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  2020 Kevin Hill
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository
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
