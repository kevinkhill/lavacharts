<?php

namespace Khill\Lavacharts\Support;

/**
 * Config Class
 *
 * Simple class to manage the default configuration of Lavacharts
 *
 * @package   Khill\Lavacharts
 * @since     3.2.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class Config
{
    /**
     * Returns the default configuration options for Lavacharts
     *
     * @return array
     */
    public static function getDefault()
    {
        return require(__DIR__.'/../Laravel/config/lavacharts.php');
    }

    /**
     * Returns a list of the options that can be set.
     *
     * @return array
     */
    public static function getOptions()
    {
        $options = self::getDefault();

        return array_keys($options);
    }
}
