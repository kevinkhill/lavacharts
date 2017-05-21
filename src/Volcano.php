<?php

namespace Khill\Lavacharts;

use Khill\Lavacharts\Values\Label;
use Khill\Lavacharts\Charts\Chart;
use Khill\Lavacharts\Charts\ChartFactory;
use Khill\Lavacharts\Dashboards\Dashboard;
use Khill\Lavacharts\Exceptions\ChartNotFound;
use Khill\Lavacharts\Exceptions\DashboardNotFound;
use Khill\Lavacharts\Support\Contracts\RenderableInterface as Renderable;

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
class Volcano
{
    /**
     * Holds all of the defined Charts.
     *
     * @var \Khill\Lavacharts\Charts\Chart[][]
     */
    private $charts = [];

    /**
     * Holds all of the defined Dashboards.
     *
     * @var \Khill\Lavacharts\Dashboards\Dashboard[]
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
     * @param  \Khill\Lavacharts\Support\Contracts\RenderableInterface $renderable
     * @return \Khill\Lavacharts\Support\Contracts\RenderableInterface
     */
    public function store(Renderable $renderable)
    {
        if ($renderable instanceof Dashboard) {
            $retVal = $this->storeDashboard($renderable);
        }

        if ($renderable instanceof Chart) {
            $retVal = $this->storeChart($renderable);
        }

        return $retVal;
    }

    /**
     * Fetches an existing Chart or Dashboard from the volcano storage.
     *
     * @since  3.0.3
     * @param  string                         $type  Type of Chart or Dashboard.
     * @param  \Khill\Lavacharts\Values\Label $label Label of the Chart or Dashboard.
     * @return \Khill\Lavacharts\Support\Contracts\RenderableInterface
     * @throws \Khill\Lavacharts\Exceptions\ChartNotFound
     * @throws \Khill\Lavacharts\Exceptions\DashboardNotFound
     */
    public function get($type, Label $label)
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
        $charts = [];

        foreach ($this->charts as $chartType) {
            foreach ($chartType as $chart) {
                $charts[] = $chart;
            }
        }

        return array_merge($charts, $this->dashboards);
    }

    /**
     * Simple true/false test if a chart exists.
     *
     * @param  string $type  Type of chart to check.
     * @param  Label  $label Identifying label of a chart to check.
     * @return bool
     */
    public function checkChart($type, Label $label)
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
     * @param  Label $label Identifying label of a dashboard to isNonEmpty.
     * @return bool
     */
    public function checkDashboard(Label $label)
    {
        return array_key_exists((string) $label, $this->dashboards);
    }

    /**
     * Stores a chart in the volcano and gives it back.
     *
     * @access private
     * @param  Chart $chart Chart to store in the volcano.
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
     * Retrieves a chart from the volcano.
     *
     * @access private
     * @param  string $type  Type of chart to store.
     * @param  Label  $label Identifying label for the chart.
     * @throws ChartNotFound
     * @return Chart
     */
    private function getChart($type, Label $label)
    {
        if ($this->checkChart($type, $label) === false) {
            throw new ChartNotFound($type, $label);
        }

        return $this->charts[$type][(string) $label];
    }

    /**
     * Retrieves a dashboard from the volcano.
     *
     * @access private
     * @param  Label $label Identifying label for the dashboard.
     * @throws DashboardNotFound
     * @return Dashboard
     */
    private function getDashboard(Label $label)
    {
        if ($this->checkDashboard($label) === false) {
            throw new DashboardNotFound($label);
        }

        return $this->dashboards[(string) $label];
    }
}
