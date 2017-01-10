<?php

namespace Khill\Lavacharts\Dashboards;

use \Khill\Lavacharts\Builders\DashboardBuilder;

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
     * Instance of the DashboardBuilder
     *
     * @var \Khill\Lavacharts\Builders\DashboardBuilder
     */
    private $dashBuilder;

    /**
     * DashboardFactory constructor.
     */
    public function __construct()
    {
        $this->dashBuilder = new DashboardBuilder;
    }

    /**
     * Creates and stores Dashboards
     *
     * If the Dashboard is found in the Volcano, then it is returned.
     * Otherwise, a new dashboard is created and stored in the Volcano.
     *
     * @since  3.1.0
     * @param  array $args Array of arguments from Lavacharts
     * @return \Khill\Lavacharts\Dashboards\Dashboard
     */
    public function create($args)
    {
        $this->dashBuilder->setLabel($args[0]);
        $this->dashBuilder->setDataTable($args[1]);

        if (isset($args[2])) {
            if (is_string($args[2])) {
                $this->dashBuilder->setElementId($args[2]);
            }

            if (is_array($args[2])) {
                $this->dashBuilder->setBindings($args[2]);
            }
        }

        if (isset($args[3])) {
            if (is_string($args[3])) {
                $this->dashBuilder->setElementId($args[3]);
            }

            if (is_array($args[3])) {
                $this->dashBuilder->setBindings($args[3]);
            }
        }

        return $this->dashBuilder->getDashboard();
    }
}
