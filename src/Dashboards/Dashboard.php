<?php

namespace Khill\Lavacharts\Dashboards;

use Khill\Lavacharts\Dashboards\Bindings\Binding;
use Khill\Lavacharts\Dashboards\Bindings\BindingFactory;
use Khill\Lavacharts\Dashboards\Wrappers\Wrapper;
use Khill\Lavacharts\Javascript\DashboardJsFactory;
use Khill\Lavacharts\Support\Google;
use Khill\Lavacharts\Support\Renderable;
use Khill\Lavacharts\Support\Contracts\Customizable;
use Khill\Lavacharts\Support\Contracts\DataInterface;
use Khill\Lavacharts\Support\Contracts\JsFactory;
use Khill\Lavacharts\Support\Contracts\Visualization;
use Khill\Lavacharts\Support\Traits\HasOptionsTrait as HasOptions;
use Khill\Lavacharts\Support\Traits\HasDataTableTrait as HasDataTable;
use Khill\Lavacharts\Support\StringValue as Str;

/**
 * Class Dashboard
 *
 * This class is for creating interactive charts that have controls and filters.
 *
 * The dashboard takes filters, wrapped as controls, and charts to create a dynamic
 * display of data.
 *
 * @package   Khill\Lavacharts\Dashboards
 * @since     3.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class Dashboard extends Renderable implements Customizable, JsFactory, Visualization
{
    use HasDataTable, HasOptions;

    /**
     * Javascript type.
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
     * Javascript package.
     *
     * @var string
     */
    const VISUALIZATION_PACKAGE = 'controls';

    /**
     * Binding Factory for creating new bindings
     *
     * @var BindingFactory
     */
    private $bindingFactory;

    /**
     * Array of Binding objects, mapping controls to charts.
     *
     * @var Binding[]
     */
    private $bindings = [];

    /**
     * Builds a new Dashboard
     *
     * If passed an array of bindings, they will be applied upon creation.
     *
     * @param string        $label     Label for the Dashboard
     * @param DataInterface $datatable DataInterface for the dashboard
     * @param array         $options   Element Id for the Dashboard
     */
    public function __construct($label, DataInterface $data, array $options = [])
    {
        $this->bindingFactory = new BindingFactory;

        $this->label     = Str::verify($label);
        $this->datatable = $data;

        $this->setOptions($options);

        if ($this->options->hasAndIs('elementId', 'string')) {
            $this->elementId = $this->options->elementId;
        }
    }

    /**
     * Returns the chart type.
     *
     * TODO: remove?
     * @since 3.1.0
     * @return string
     */
    public function getType()
    {
        return self::TYPE;
    }

    /**
     * Returns the javascript visualization package name
     *
     * @return string
     */
    public function getJsPackage()
    {
        return self::VISUALIZATION_PACKAGE;
    }

    /**
     * Returns the javascript visualization class name
     *
     * @return string
     */
    public function getJsClass()
    {
        return Google::STANDARD_NAMESPACE . self::TYPE;
    }

    /**
     * Get the JsFactory for the chart.
     *
     * @return DashboardJsFactory
     */
    public function getJsFactory()
    {
        return new DashboardJsFactory($this);
    }

    /**
     * Fetch the dashboard's bound charts from the wrappers.
     *
     * @return array
     */
    public function getBoundCharts()
    {
        // TODO: test this
        return array_map(function (Binding $binding) {
            return array_map(function (Wrapper $chartWrapper) {
                return $chartWrapper->unwrap();
            }, $binding->getChartWrappers());
        }, $this->bindings);

//        $charts = [];
//
//        foreach ($this->bindings as $binding) {
//            foreach ($binding->getChartWrappers() as $chartWrapper) {
//                $chart = $chartWrapper->unwrap();
//
//                $charts[] = $chart;
//            }
//        }
//
//        return $charts;
    }

    /**
     * Binds ControlWrappers to ChartWrappers in the dashboard.
     *
     * - A OneToOne binding is created if single wrappers are passed.
     * - If a single ControlWrapper is passed with an array of ChartWrappers,
     *   a OneToMany binding is created.
     * - If an array of ControlWrappers is passed with one ChartWrapper, then
     *   a ManyToOne binding is created.
     * - If an array of ControlWrappers is passed with and array of ChartWrappers, then
     *   a ManyToMany binding is created.
     *
     * @param  \Khill\Lavacharts\Dashboards\Wrappers\ControlWrapper|array $controlWraps
     * @param  \Khill\Lavacharts\Dashboards\Wrappers\ChartWrapper|array   $chartWraps
     * @return \Khill\Lavacharts\Dashboards\Dashboard
     * @throws \Khill\Lavacharts\Exceptions\InvalidBindings
     */
    public function bind($controlWraps, $chartWraps)
    {
        $this->bindings[] = $this->bindingFactory->create($controlWraps, $chartWraps);

        return $this;
    }

    /**
     * Fetch the dashboard's bindings.
     *
     * @return Binding[]
     */
    public function getBindings()
    {
        return $this->bindings;
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
        $this->bindings = array_map(function (array $bindingPair) {
            return $this->bindingFactory->create($bindingPair[0], $bindingPair[1]);
        }, $bindings);

        return $this;
    }

    /**
     * Returns the type of renderable.
     *
     * @return string
     */
    public function getRenderableType()
    {
        // TODO: Implement getRenderableType() method.
    }

    /**
     * Array representation of the Chart.
     *
     * @return array
     */
    public function toArray()
    {
        // TODO: Implement toArray() method.
    }
}
