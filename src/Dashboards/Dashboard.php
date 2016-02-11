<?php

namespace Khill\Lavacharts\Dashboards;

use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Values\ElementId;
use \Khill\Lavacharts\Dashboards\Bindings\BindingFactory;

class Dashboard
{
    use \Khill\Lavacharts\Traits\LabelTrait;
    use \Khill\Lavacharts\Traits\ElementIdTrait;

    /**
     * Javascript chart type.
     *
     * @var string
     */
    const TYPE = 'Dashboard';

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
     * Binding Factory for creating new bindings
     *
     * @var \Khill\Lavacharts\Dashboards\Bindings\BindingFactory
     */
    private $BindingFactory;

    /**
     * Array of Binding objects, mapping controls to charts.
     *
     * @var array
     */
    private $bindings;

    /**
     * Builds a new Dashboard
     *
     * If passed an array of bindings, they will be applied upon instansiation.
     *
     * @param \Khill\Lavacharts\Values\Label $label    Label for the Dashboard
     * @param array                          $bindings Array of bindings to apply
     */
    public function __construct(Label $label, $bindings = [])
    {
        $this->bindingFactory = new BindingFactory;

        $this->label    = $label;
        $this->bindings = [];

        if (empty($bindings) === false) {
            $this->setBindings($bindings);
        }
    }

    /**
     * Returns the chart type.
     *
     * @since 3.1.0
     * @return string
     */
    public function getType()
    {
        return static::TYPE;
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
    public function setBindings($bindings)
    {
        $this->bindings = $this->bindingFactory->creatFromArray($bindings);

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
     * Binds ControlWrappers to ChartWrappers in the dashboard.
     * - A OneToOne binding is created if single wrappers are passed.
     * - If a single ControlWrapper is passed with an array of ChartWrappers,
     *   a OneToMany binding is created.
     * - If an array of ControlWrappers is passed with one ChartWrapper, then
     *   a ManyToOne binding is created.
     *
     * @param  \Khill\Lavacharts\Dashboards\ControlWrapper|array $controlWraps
     * @param  \Khill\Lavacharts\Dashboards\ChartWrapper|array   $chartWraps
     * @return \Khill\Lavacharts\Dashboards\Dashboard
     * @throws \Khill\Lavacharts\Exceptions\InvalidBindings
     */
    public function bind($controlWraps, $chartWraps)
    {
        $this->bindings[] = $this->bindingFactory->create($controlWraps, $chartWraps);

        return $this;
    }
}
