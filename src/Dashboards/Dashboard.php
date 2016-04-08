<?php

namespace Khill\Lavacharts\Dashboards;

use Khill\Lavacharts\Values\Label;
use Khill\Lavacharts\Builders\DashboardBuilder;
use Khill\Lavacharts\Dashboards\Bindings\BindingFactory;

class Dashboard
{
    /**
     * Google's dashboard version
     *
     * @var string
     */
    const VERSION = '1';

    /**
     * Javascript chart package.
     *
     * @var string
     */
    const VIZ_PACKAGE = 'controls';

    /**
     * Javascript chart class.
     *
     * @var string
     */
    const VIZ_CLASS = 'google.visualization.Dashboard';

    /**
     * The dashboard's unique label.
     *
     * @var \Khill\Lavacharts\Values\Label
     */
    private $label = null;

    /**
     * Array of Binding objects, mapping controls to charts.
     *
     * @var array
     */
    private $bindings = [];

    /**
     * Builds a new Dashboard with identifying label.
     *
     * @param \Khill\Lavacharts\Values\Label $label
     * @param array                          $bindings
     */
    public function __construct(Label $label, array $bindings = [])
    {
        $this->label = $label;

        if (empty($bindings) === false) {
            $this->setBindings($bindings);
        }
    }
    /**
     * Creates a new Dashboard from the given arguments.
     *
     * @since  3.0.3
     * @param  array $args Array of arguments for the dash
     * @return \Khill\Lavacharts\Dashboards\Dashboard
     */
    public static function Factory($args)
    {
        $dashboard = new DashboardBuilder;

        $dashboard->setLabel($args[0]);

        if (isset($args[1]) && is_array($args[1])) {
            $dashboard->setBindings($args[1]);
        }

        return $dashboard->getDashboard();
    }

    /**
     * Binds ControlWrappers to ChartWrappers in the dashboard.
     * - A OneToOne binding is created if single wrappers are passed.
     * - If a single ControlWrapper is passed with an array of ChartWrappers,
     *   a OneToMany binding is created.
     * - If an array of ControlWrappers is passed with one ChartWrapper, then
     *   a ManyToOne binding is created.
     *
     * @uses   \Khill\Lavacharts\Dashboard\Bindings\BindingFactory
     * @param  \Khill\Lavacharts\Dashboards\ControlWrapper|array $controlWraps
     * @param  \Khill\Lavacharts\Dashboards\ChartWrapper|array   $chartWraps
     * @return \Khill\Lavacharts\Dashboards\Dashboard
     * @throws \Khill\Lavacharts\Exceptions\InvalidBindings
     */
    public function bind($controlWraps, $chartWraps)
    {
        $this->bindings[] = BindingFactory::create($controlWraps, $chartWraps);

        return $this;
    }

    /**
     * Fetch the dashboard's bindings.
     *
     * @return array
     */
    public function getBindings()
    {
        return $this->bindings;
    }

    /**
     * Fetch the dashboard's bound charts from the wrappers.
     *
     * @return array
     */
    public function getBoundCharts()
    {
        $charts = [];

        foreach ($this->bindings as $binding) {
            foreach ($binding->getChartWrappers() as $chartWrapper) {
                $chart = $chartWrapper->unwrap();

                $charts[$chart::TYPE] = $chart;
            }
        }

        return $charts;
    }

    /**
     * Returns the dashboard label.
     *
     * @return \Khill\Lavacharts\Values\Label
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Batch add an array of bindings.
     *
     * This method can set all bindings at once instead of chaining multiple bind methods.
     *
     * @param  array $bindings
     * @return \Khill\Lavacharts\Dashboards\Dashboard
     * @throws \Khill\Lavacharts\Exceptions\InvalidBindings
     */
    public function setBindings(array $bindings)
    {
        foreach ($bindings as $binding) {
            $this->bind($binding[0], $binding[1]);
        }

        return $this;
    }
}
