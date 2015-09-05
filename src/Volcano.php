<?php

namespace Khill\Lavacharts;

use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Charts\Chart;
use \Khill\Lavacharts\Dashboards\Dashboard;
use \Khill\Lavacharts\Exceptions\ChartNotFound;
use \Khill\Lavacharts\Exceptions\DashboardNotFound;

/**
 * Volcano Storage Class
 *
 * Storage class that holds all defined charts and dashboards.
 *
 * @category  Class
 * @package   Khill\Lavacharts
 * @since     2.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2015, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT MIT
 */
class Volcano
{
    /**
     * Holds all of the defined Charts.
     *
     * @var array
     */
    private $charts = [];

    /**
     * Holds all of the defined Dashboards.
     *
     * @var array
     */
    private $dashboards = [];

    /**
     * Stores a chart in the volcano datastore.
     *
     * @param  \Khill\Lavacharts\Charts\Chart $chart Chart to store in the volcano.
     * @return boolean
     */
    public function storeChart(Chart $chart)
    {
        $this->charts[$chart::TYPE][(string) $chart->getLabel()] = $chart;

        return true;
    }

    /**
     * Stores a dashboard in the volcano datastore.
     *
     * @param  \Khill\Lavacharts\Dashboards\Dashboard $dashboard Dashboard to store in the volcano.
     * @return boolean
     */
    public function storeDashboard(Dashboard $dashboard)
    {
        $this->dashboards[(string) $dashboard->getLabel()] = $dashboard;

        return true;
    }

    /**
     * Retrieves a chart from the volcano datastore.
     *
     * @param  string $type  Type of chart to store.
     * @param  \Khill\Lavacharts\Values\Label $label Identifying label for the chart.
     * @throws \Khill\Lavacharts\Exceptions\ChartNotFound
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function getChart($type, Label $label)
    {
        if ($this->checkChart($type, $label) === false) {
            throw new ChartNotFound($type, $label);
        }

        return $this->charts[$type][(string) $label];
    }

    /**
     * Retrieves a dashboard from the volcano datastore.
     *
     * @param  \Khill\Lavacharts\Values\Label $label Identifying label for the dashboard.
     * @throws \Khill\Lavacharts\Exceptions\DashboardNotFound
     * @return \Khill\Lavacharts\Dashboards\Dashboard
     */
    public function getDashboard(Label $label)
    {
        if ($this->checkDashboard($label) === false) {
            throw new DashboardNotFound($label);
        }

        return $this->dashboards[(string) $label];
    }

    /**
     * Simple true/false test if a chart exists.
     *
     * @param  string $type  Type of chart to check.
     * @param  \Khill\Lavacharts\Values\Label $label Identifying label of a chart to check.
     * @return boolean
     */
    public function checkChart($type, Label $label)
    {
        if (Utils::nonEmptyString($type) === false) {
            return false;
        }

        if (array_key_exists($type, $this->charts) === false) {
            return false;
        }

        return array_key_exists((string) $label, $this->charts[$type]);
    }

    /**
     * Simple true/false test if a dashboard exists.
     *
     * @param  \Khill\Lavacharts\Values\Label $label Identifying label of a chart to check.
     * @return boolean
     */
    public function checkDashboard(Label $label)
    {
        return array_key_exists((string) $label, $this->dashboards);
    }
}
