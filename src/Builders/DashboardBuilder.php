<?php

namespace Khill\Lavacharts\Builders;

use Khill\Lavacharts\Dashboards\Dashboard;

/**
 * Class DashboardBuilder
 *
 * This class is used to build dashboards by setting the properties, instead of trying to cover
 * everything in the constructor.
 *
 * @package    Khill\Lavacharts\Builders
 * @since      3.1.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2016, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class DashboardBuilder extends GenericBuilder
{
    /**
     * Bindings to use for the dashboard.
     *
     * @var \Khill\Lavacharts\Dashboards\Bindings\Binding[]
     */
    private $bindings = [];

    /**
     * Set the bindings for the Dashboard.
     *
     * @param \Khill\Lavacharts\Dashboards\Bindings\Binding[] $bindings Array of bindings
     * @return self
     */
    public function setBindings(array $bindings)
    {
        $this->bindings = $bindings;

        return $this;
    }

    /**
     * Returns the built Dashboard.
     *
     * @return \Khill\Lavacharts\Dashboards\Dashboard
     */
    public function getDashboard()
    {
        return new Dashboard(
            $this->label,
            $this->bindings,
            $this->elementId
        );
    }
}
