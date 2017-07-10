<?php

namespace Khill\Lavacharts\Support;

if (!function_exists('google_visualization')) {
    function google_visualization($class) {
        return Google::STANDARD_NAMESPACE . $class;
    }
}

/**
 * Google Class
 *
 * Simple support class for storing things google related.
 *
 * @package   Khill\Lavacharts\Support
 * @since     3.2.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class Google
{
    const MATERIAL_NAMESPACE = 'google.charts.';

    const STANDARD_NAMESPACE = 'google.visualization.';

    /**
     * Build the material namespace string from a class.
     *
     * @param string $class
     * @return string
     */
    public static function charts($class)
    {
        return static::MATERIAL_NAMESPACE . $class;
    }

    /**
     * Build the standard namespace string from a class.
     *
     * @param string $class
     * @return string
     */
    public static function visualization($class)
    {
        return static::STANDARD_NAMESPACE . $class;
    }
}

if (!function_exists('google_visualization')) {
    function google_visualization($class) {
        return Google::STANDARD_NAMESPACE . $class;
    }
}
