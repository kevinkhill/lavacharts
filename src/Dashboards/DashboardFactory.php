<?php

namespace Khill\Lavacharts\Dashboards;

use Khill\Lavacharts\Builders\DashboardBuilder;
use Khill\Lavacharts\Support\Args;
use Khill\Lavacharts\Support\Options;
use Khill\Lavacharts\Support\StringValue as Str;

/**
 * DashboardFactory Class
 *
 * Used for creating new dashboards and removing the need for the main Lavacharts
 * class to handle the creation.
 *
 *
 * @category  Class
 * @package   Khill\Lavacharts\Dashboards
 * @since     3.1.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class DashboardFactory
{
    /**
     * Creates and stores Dashboards
     *
     * If the Dashboard is found in the Volcano, then it is returned.
     * Otherwise, a new dashboard is created and stored in the Volcano.
     *
     * @since  3.1.0
     * @param  array $args Array of arguments from Lavacharts
     * @return Dashboard
     */
    public static function create($label, $args)
    {
        $dashBuilder = new DashboardBuilder;

        list($data, $options) = $args;

        $label = Str::verify($label);

        $dashBuilder->setLabel($label);
        $dashBuilder->setDataTable($data);

        if (isset($options)) {
            // If the 3rd constructor param is a string, assume elementId
            if (is_string($options)) {
                $dashBuilder->setElementId($options);
            }

            // Process options if the 3rd parameter is an array
            if (is_array($options)) {
                $options = new Options($options);

                if ($options->hasAndIs('elementId', 'string')) {
                    $dashBuilder->setElementId($options->elementId);
                }

                if ($options->hasAndIs('bindings', 'array')) {
                    $dashBuilder->setBindings($options->bindings);
                }
            }
        }

        return $dashBuilder->getDashboard();
    }
}
