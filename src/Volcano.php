<?php

namespace Khill\Lavacharts;


use Khill\Lavacharts\Charts\Chart;
use Khill\Lavacharts\Charts\ChartFactory;
use Khill\Lavacharts\Dashboards\Dashboard;
use Khill\Lavacharts\Exceptions\ChartNotFound;
use Khill\Lavacharts\Exceptions\DashboardNotFound;
use Khill\Lavacharts\Support\Renderable;
use Khill\Lavacharts\Support\Contracts\Arrayable;
use Khill\Lavacharts\Support\Contracts\Jsonable;

/**
 * Class Volcano
 *
 * Storage class that holds all defined charts and dashboards.
 *
 * @package   Khill\Lavacharts
 * @since     2.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class Volcano implements Arrayable, Jsonable
{
    /**
     * Holds all of the defined Charts.
     *
     * @var Chart[][]
     */
    private $charts = [];

    /**
     * Holds all of the defined Dashboards.
     *
     * @var Dashboard[]
     */
    private $dashboards = [];

    /**
     * Volcano constructor
     */
    public function __construct()
    {
        foreach (ChartFactory::getChartTypes() as $chartType) {
            $this->charts[$chartType] = [];
        }
    }

    /**
     * Stores a Chart or Dashboard in the Volcano.
     *
     * @since  3.0.3
     * @param  Renderable $renderable
     * @return Chart|Dashboard
     */
    public function store(Renderable $renderable)
    {
        if ($renderable instanceof Dashboard) {
            $this->storeDashboard($renderable);
        }

        if ($renderable instanceof Chart) {
            $this->storeChart($renderable);
        }

        return $renderable;
    }

    /**
     * Fetches an existing Chart or Dashboard from the volcano storage.
     *
     * @since  3.0.3
     * @param  string $type  Type of Chart or Dashboard.
     * @param  string $label Label of the Chart or Dashboard.
     * @return Chart|Dashboard
     * @throws ChartNotFound
     * @throws DashboardNotFound
     */
    public function get($type, $label)
    {
        if ($type == 'Dashboard') {
            return $this->getDashboard($label);
        } else {
            return $this->getChart($type, $label);
        }
    }

    /**
     * Returns all stored charts and dashboards
     *
     * @since  3.0.3
     * @return Renderable[]
     */
    public function getAll()
    {
        return array_merge($this->getCharts(), $this->dashboards);
    }

    /**
     * Simple true/false test if a chart exists.
     *
     * @param  string $type  Type of chart to check.
     * @param  string $label Identifying label of a chart to check.
     * @return bool
     */
    public function checkChart($type, $label)
    {
        if (ChartFactory::isValidChart($type) === false) {
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
     * @param  string $label Identifying label of a dashboard to isNonEmpty.
     * @return bool
     */
    public function checkDashboard($label)
    {
        return array_key_exists((string) $label, $this->dashboards);
    }

    /**
     * Retrieves all the charts from the volcano.
     *
     * @access private
     * @return Chart[]
     */
    public function getCharts()
    {
        $charts = [];

        array_walk_recursive($this->charts, function (Chart $chart) use (&$charts) {
            $charts[] = $chart;
        });

        return $charts;
    }

    /**
     * Retrieves a chart from the volcano.
     *
     * @access private
     * @param  string $type  Type of chart to store.
     * @param  string $label Identifying label for the chart.
     * @throws ChartNotFound
     * @return Chart
     */
    private function getChart($type, $label)
    {
        if ($this->checkChart($type, $label) === false) {
            throw new ChartNotFound($type, $label);
        }

        return $this->charts[$type][(string) $label];
    }

    /**
     * Retrieves all the dashboards from the volcano.
     *
     * @return Dashboard[]
     */
    public function getDashboards()
    {
        return $this->dashboards;
    }

    /**
     * Retrieves a dashboard from the volcano.
     *
     * @access private
     * @param  string $label Identifying label for the dashboard.
     * @throws DashboardNotFound
     * @return Dashboard
     */
    private function getDashboard($label)
    {
        if ($this->checkDashboard($label) === false) {
            throw new DashboardNotFound($label);
        }

        return $this->dashboards[(string) $label];
    }

    /**
     * Stores a chart in the volcano and gives it back.
     *
     * @access private
     * @param  Chart $chart
     * @return Chart
     */
    private function storeChart(Chart $chart)
    {
        $this->charts[$chart->getType()][(string) $chart->getLabel()] = $chart;

        return $chart;
    }

    /**
     * Stores a dashboard in the volcano and gives it back.
     *
     * @access private
     * @param  Dashboard $dashboard Dashboard to store in the volcano.
     * @return Dashboard
     */
    private function storeDashboard(Dashboard $dashboard)
    {
        $this->dashboards[(string) $dashboard->getLabel()] = $dashboard;

        return $dashboard;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getAll();
    }

    /**
     * Returns a customize JSON representation of an object.
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this);
    }

    /**
     * Custom serialization of the object.
     */
    function jsonSerialize()
    {
        return $this->toArray();
    }
}
