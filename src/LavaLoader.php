<?php

namespace Khill\Lavacharts;

use Khill\Lavacharts\Support\Psr4Autoloader;

/**
 * LavaLoader is a wrapper around the Psr4 autoloader to enable the use of Lavacharts without having to use Composer.
 *
 *
 * @package       Khill\Lavacharts
 * @author        Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link          http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link          http://lavacharts.com                   Official Docs Site
 * @license       http://opensource.org/licenses/MIT      MIT
 */
class LavaLoader
{
    public static function register()
    {
        require_once(__DIR__ . '/Support/Psr4Autoloader.php');

        $loader = new Psr4Autoloader;
        $loader->register();
        $loader->addNamespace('Khill\Lavacharts', __DIR__);
    }
}
