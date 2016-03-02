<?php

namespace Khill\Lavacharts\Dashboards;

use \Khill\Lavacharts\Volcano;
use \Khill\Lavacharts\Builders\DashboardBuilder;

//@TODO phpdocs
class DashboardFactory
{
    /**
     * Holds all of the defined Charts and DataTables.
     *
     * @var \Khill\Lavacharts\Volcano
     */
    private $volcano;

    /**
     * Instance of the DashboardBuilder for, well, building charts.
     *
     * @var \Khill\Lavacharts\Builders\DashboardBuilder
     */
    private $dashBuilder;

    /**
     * DashboardFactory constructor.
     *
     * @param \Khill\Lavacharts\Volcano $volcano
     */
    public function __construct(Volcano $volcano)
    {
        $this->volcano     = $volcano;
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
        $dashboard = $this->dashBuilder
                          ->setLabel($args[0])
                          ->setBindings($args[1])
                          ->setElementId($args[2])
                          ->getDashboard();

        if ($this->volcano->checkDashboard($label)) {
        }

        return $this->volcano->store($dashboard);
    }
}
