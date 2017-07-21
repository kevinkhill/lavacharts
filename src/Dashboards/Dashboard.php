<?php

namespace Khill\Lavacharts\Dashboards;

use Khill\Lavacharts\Charts\Chart;
use Khill\Lavacharts\Dashboards\Bindings\Binding;
use Khill\Lavacharts\Dashboards\Bindings\BindingFactory;
use Khill\Lavacharts\Exceptions\RenderingException;
use Khill\Lavacharts\Support\Buffer;
use Khill\Lavacharts\Support\Contracts\Customizable;
use Khill\Lavacharts\Support\Contracts\DataInterface;
use Khill\Lavacharts\Support\Contracts\Javascriptable;
use Khill\Lavacharts\Support\Contracts\Visualization;
use Khill\Lavacharts\Support\Renderable;
use Khill\Lavacharts\Support\StringValue as Str;
use Khill\Lavacharts\Support\Traits\HasDataTableTrait as HasDataTable;
use Khill\Lavacharts\Support\Traits\HasOptionsTrait as HasOptions;
use Khill\Lavacharts\Support\Traits\ToJavascriptTrait as ToJavascript;

/**
 * Class Dashboard
 *
 * This class is for creating interactive charts that have controls and filters.
 *
 * The dashboard takes filters, wrapped as controls, and charts to create a dynamic
 * display of data.
 *
 * @package       Khill\Lavacharts\Dashboards
 * @since         3.0.0
 * @author        Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link          http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link          http://lavacharts.com                   Official Docs Site
 * @license       http://opensource.org/licenses/MIT      MIT
 */
class Dashboard extends Renderable implements Customizable, Javascriptable, Visualization
{
    use HasDataTable, HasOptions, ToJavascript;

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
    public function __construct($label, DataInterface $data, $options = [])
    {
        $this->label     = Str::verify($label);
        $this->datatable = $data;

        $this->setOptions($options);

        if ($this->options->hasAndIs('elementId', 'string')) {
            $this->elementId = $this->options->elementId;

            $this->options->forget('elementId');
        }

        if ($this->options->hasAndIs('bindings', 'array')) {
            $this->setBindings($this->options->bindings);
        }
    }

    /**
     * Returns the Dashboard version
     *
     * @since 4.0.0
     * @return string
     */
    public function getVersion()
    {
        return '1';
    }

    /**
     * Returns the javascript visualization package name
     *
     * @return string
     */
    public function getJsPackage()
    {
        return 'controls';
    }

    /**
     * Returns the javascript visualization class name
     *
     * @return string
     */
    public function getJsClass()
    {
        return self::GOOGLE_VISUALIZATION . 'Dashboard';
    }

    /**
     * Check if the Dashboard has any bindings.
     *
     * @since 4.0.0
     * @return bool
     */
    public function hasBindings()
    {
        return count($this->bindings) > 0;
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
     * @throws \Khill\Lavacharts\Exceptions\BindingException
     */
    public function bind($controlWraps, $chartWraps)
    {
        $this->bindings[] = BindingFactory::create($controlWraps, $chartWraps);

        return $this;
    }

    /**
     * Batch add an array of bindings.
     *
     * This method can set all bindings at once instead of chaining multiple bind methods.
     *
     * @param  array $bindings
     * @return \Khill\Lavacharts\Dashboards\Dashboard
     * @throws \Khill\Lavacharts\Exceptions\BindingException
     */
    public function setBindings(array $bindings)
    {
        foreach ($bindings as $binding) {
            return $this->bind($binding[0], $binding[1]);
        }

        return $this;
    }

    /**
     * Get all the packages need to render the Dashboard.
     *
     * @since 4.0.0
     * @return array
     */
    public function getPackages()
    {
        $packages = [
            $this->getJsPackage(),
        ];

        foreach ($this->getBoundCharts() as $chart) {
            array_push($packages, $chart->getJsPackage());
        }

        return array_unique($packages);
    }

    /**
     * Fetch the dashboard's bound charts from the wrappers.
     *
     * @return Chart[]
     */
    protected function getBoundCharts()
    {
        $charts = [];

        foreach ($this->bindings as $binding) {
            foreach ($binding->getChartWrappers() as $chartWrapper) {
                $chart = $chartWrapper->unwrap();

                $charts[] = $chart;
            }
        }

        return $charts;
    }

    /**
     * Process all the bindings for a Dashboard.
     *
     * Turns the chart and control wrappers into new Google Visualization Objects.
     *
     * @access private
     * @return Buffer
     * @throws RenderingException
     */
    protected function getBindingsBuffer()
    {
        if (! $this->hasBindings()) {
            throw new RenderingException('Dashboards without bindings cannot be rendered.');
        }

        $buffer = new Buffer();

        foreach ($this->bindings as $binding) {
            $buffer->append($binding);
        }

        return $buffer;
    }

    /**
     * Convert the Chart to Javascript.
     *
     * @return string
     */
    public function toJavascript()
    {
        return sprintf($this->getJavascriptFormat(), $this->getJavascriptSource());
    }

    /**
     * @inheritdoc
     */
    public function getJavascriptSource()
    {
        return $this->toJson();
    }

    /**
     * @inheritdoc
     */
    public function getJavascriptFormat()
    {
        return 'window.lava.addNewDashboard(%s);';
    }

    /**
     * Array representation of the Dashboard.
     *
     * @since 4.0.0 Removed unnecessary info from array.
     * @since 4.0.0
     * @return array
     */
    public function toArray()
    {
        return [
            'label'     => $this->label,
            'elementId' => $this->elementId,
            'bindings'  => $this->bindings,
            'datatable' => $this->datatable,
            'packages'  => $this->getPackages()
//            'version'   => $this->getVersion(),
//            'class'     => $this->getJsClass(),
//            'bindings'  => $this->getBindingsBuffer(),
        ];
    }
}
