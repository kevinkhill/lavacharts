<?php

namespace Khill\Lavacharts;

use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Values\ElementId;
use \Khill\Lavacharts\Charts\Chart;
use \Khill\Lavacharts\Charts\ChartFactory;
use \Khill\Lavacharts\Configs\Renderable;
use \Khill\Lavacharts\Dashboards\Dashboard;
use \Khill\Lavacharts\Dashboards\ChartWrapper;
use \Khill\Lavacharts\Dashboards\ControlWrapper;
use \Khill\Lavacharts\Dashboards\Filters\Filter;
use \Khill\Lavacharts\Dashboards\Filters\FilterFactory;
use \Khill\Lavacharts\DataTables\DataFactory;
use \Khill\Lavacharts\Javascript\JavascriptFactory;
use \Khill\Lavacharts\Exceptions\InvalidLavaObject;
use \Khill\Lavacharts\Exceptions\InvalidFilterObject;
use \Khill\Lavacharts\Exceptions\InvalidFunctionParam;
use \Khill\Lavacharts\Html\HtmlFactory;

/**
 * Lavacharts - A PHP wrapper library for the Google Chart API
 *
 *
 * @category  Class
 * @package   Khill\Lavacharts
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2016, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT MIT
 */
class Lavacharts
{
    /**
     * Lavacharts version
     */
    const VERSION = '3.1.0';

    /**
     * Holds all of the defined Charts and DataTables.
     *
     * @var \Khill\Lavacharts\Volcano
     */
    private $volcano;

    /**
     * JavascriptFactory for outputting lava.js and chart/dashboard javascript
     *
     * @var \Khill\Lavacharts\Javascript\JavascriptFactory
     */
    private $jsFactory;

    /**
     * ChartFactory for checking parameters and creating new charts.
     *
     * @var \Khill\Lavacharts\Charts\ChartFactory
     */
    private $chartFactory;

    /**
     * Types of column formatters.
     *
     * @var array
     */
    private $formatClasses = [
        'ArrowFormat',
        'BarFormat',
        'DateFormat',
        'NumberFormat'
    ];

    /**
     * Creates Volcano & Javascript Factory
     *
     * @return Lavacharts
     */
    public function __construct()
    {
        if (!$this->usingComposer()) {
            require_once(__DIR__.'/Psr4Autoloader.php');

            $loader = new Psr4Autoloader;
            $loader->register();
            $loader->addNamespace('Khill\Lavacharts', __DIR__);
        }

        $this->volcano      = new Volcano;
        $this->html         = new HtmlFactory;
        $this->jsFactory    = new JavascriptFactory;
        $this->chartFactory = new ChartFactory($this->volcano);
    }

    /**
     * Magic function to reduce repetitive coding and create aliases.
     *
     * @access public
     * @since  1.0.0
     * @param  string $method    Name of method
     * @param  array  $args Passed arguments
     * @throws \Khill\Lavacharts\Exceptions\InvalidLabel
     * @throws \Khill\Lavacharts\Exceptions\InvalidLavaObject
     * @throws \Khill\Lavacharts\Exceptions\InvalidFilterObject
     * @throws \Khill\Lavacharts\Exceptions\InvalidFunctionParam
     * @return mixed Returns Charts, DataTables, and Config Objects, Events, Filters
     */
    public function __call($method, $args)
    {
        //Rendering Aliases
        if ((bool) preg_match('/^render/', $method) === true) {
            $type = str_replace('render', '', $method);

            if ($type !== 'Dashboard' && in_array($type, $this->chartClasses, true) === false) {
                throw new InvalidLavaObject($type);
            }

            $lavaClass = $this->render($type, $args[0], $args[1]);
        }

        //Charts
        if (in_array($method, $this->chartFactory->getChartTypes())) {
            if ($this->exists($method, $args[0])) {
                $lavaClass = $this->fetch($method, $args[0]);
            } else {
                $lavaClass = $this->chartFactory->create($method, $args);
            }
        }

        //Formats
        if (in_array($method, $this->formatClasses)) {
            $lavaClass = $this->formatFactory($method, $args);
        }

        //Filters
        if ((bool) preg_match('/Filter$/', $method)) {
            $type   = strtolower(str_replace('Filter', '', $method));
            $config = isset($args[1]) ? $args[1] : [];

            $lavaClass = FilterFactory::create($type, $args[0], $config);
        }

        if (isset($lavaClass) == false) {
            throw new InvalidLavaObject($method);
        }

        return $lavaClass;
    }

    /**
     * Create a new DataTable with the DataFactory
     *
     * If the additional DataTablePlus package is available, then one will
     * be created, otherwise a standard DataTable is returned.
     *
     * @since  3.1.0
     * @uses   \Khill\Lavacharts\DataTables\DataFactory
     * @param  string $timezone
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public function DataTable($timezone = null)
    {
        return DataFactory::DataTable($timezone);
    }

    /**
     * Get an instance of the DataFactory
     *
     * @since  3.1.0
     * @return \Khill\Lavacharts\DataTables\DataFactory
     */
    public function DataFactory()
    {
        return new \Khill\Lavacharts\DataTables\DataFactory;
    }

    /**
     * Create a new Dashboard
     *
     * As of 3.1.0 you can pass in an array of bindings instead of using the
     * bind method.
     *
     * @since  3.1.0
     * @param  string $label Label to give the Dashboard
     * @param  array  $bindings Array of bindings to apply
     * @return \Khill\Lavacharts\DataTables\DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidLabel
     */
    public function Dashboard($labelStr, $bindings = [], $elemId = '')
    {
        $label = new Label($labelStr);

        return $this->dashboardFactory($label, $bindings);
    }

    /**
     * Create a new ControlWrapper from a Filter
     *
     * @since  3.0.0
     * @uses   \Khill\Lavacharts\Values\ElementId
     * @param  \Khill\Lavacharts\Dashboards\Filters\Filter $filter Filter to wrap
     * @param  string $elementId HTML element ID to output the control.
     * @return \Khill\Lavacharts\Dashboards\ControlWrapper
     * @throws \Khill\Lavacharts\Exceptions\InvalidElementId
     */
    public function ControlWrapper(Filter $filter, $elementIdStr)
    {
        $elementId = new ElementId($elementIdStr);

        return new ControlWrapper($filter, $elementId);
    }

    /**
     * Create a new ChartWrapper from a Chart
     *
     * @since  3.0.0
     * @uses   \Khill\Lavacharts\Values\ElementId
     * @param  \Khill\Lavacharts\Charts\Chart $chart Chart to wrap
     * @param  string $elementId HTML element ID to output the control.
     * @return \Khill\Lavacharts\Dashboards\ChartWrapper
     * @throws \Khill\Lavacharts\Exceptions\InvalidElementId
     */
    public function ChartWrapper(Chart $chart, $elementIdStr)
    {
        $elementId = new ElementId($elementIdStr);

        return new ChartWrapper($chart, $elementId);
    }

    /**
     * Renders Charts or Dashboards into the page
     *
     * Given a type, label, and HTML element id, this will output
     * all of the necessary javascript to generate the chart or dashboard.
     *
     * @since  2.0.0
     * @uses   \Khill\Lavacharts\Values\Label
     * @uses   \Khill\Lavacharts\Values\ElementId
     * @param  string $type Type of object to render.
     * @param  string $label Label of the object to render.
     * @param  string $elementId HTML element id to render into.
     * @param  mixed  $divDimensions Set true for div creation, or pass an array with height & width
     * @return string
     * @throws \Khill\Lavacharts\Exceptions\InvalidLabel
     * @throws \Khill\Lavacharts\Exceptions\InvalidElementId
     */
    public function render($type, $labelStr, $divDimensions = false)
    {
        $label = new Label($labelStr);

        if ($type == 'Dashboard') {
            $output = $this->renderDashboard($label);
        } else {
            $output = $this->renderChart($type, $label, $divDimensions);
        }

        return $output;
    }

    /**
     * Renders all charts and dashboards that have been defined
     *
     * No more individually calling render with chart types and titles.
     * The single method call in the view will parse all charts and dashboards
     * into javascript and output all the script tags.
     *
     * @since  3.1.0
     * @return string Javascript blocks
     */
    public function renderAll()
    {
        if ($this->jsFactory->coreJsRendered() === false) {
            $output = $this->jsFactory->getCoreJs();
        } else {
            $output = '';
        }

        $lavaObjects = $this->volcano->getAll();

        foreach ($lavaObjects as $resource) {
            if ($resource instanceof Dashboard) {
                $output .= $this->jsFactory->getDashboardJs($resource);
            }

            if ($resource instanceof Chart) {
                $output .= $this->jsFactory->getChartJs($resource);
            }
        }

        return $output;
    }

    /**
     * Renders the chart into the page
     *
     * Given a chart label and an HTML element id, this will output
     * all of the necessary javascript to generate the chart.
     *
     *
     * @access private
     * @since  3.0.0
     * @param  string                             $type
     * @param  \Khill\Lavacharts\Values\Label     $label
     * @param  \Khill\Lavacharts\Values\ElementId $elementId     HTML element id to render the chart into.
     * @param  mixed                              $divDimensions Set true for div creation, or pass an array with height & width
     * @return string Javascript output
     * @throws \Khill\Lavacharts\Exceptions\ChartNotFound
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidDivDimensions
     */
    private function renderChart($type, Label $label, $divDimensions = false)
    {
        $jsOutput = '';

        if ($this->jsFactory->coreJsRendered() === false) {
            $jsOutput = $this->jsFactory->getCoreJs();
        }

        $chart = $this->volcano->get($type, $label);

        if ($divDimensions !== false) {
            $jsOutput .= $this->html->createDiv($chart->getElementId(), $divDimensions);
        }

        $jsOutput .= $this->jsFactory->getChartJs($chart);

        return $jsOutput;
    }

    /**
     * Renders the chart into the page
     *
     * Given a chart label and an HTML element id, this will output
     * all of the necessary javascript to generate the chart.
     *
     * @access private
     * @since  3.0.0
     * @param  \Khill\Lavacharts\Values\Label     $chartLabel Label of a saved chart.
     * @param  \Khill\Lavacharts\Values\ElementId $elementId  HTML element id to render the chart into.
     * @return string Javascript output
     * @throws \Khill\Lavacharts\Exceptions\DashboardNotFound
     */
    private function renderDashboard(Label $label)
    {
        $jsOutput = '';

        if ($this->jsFactory->coreJsRendered() === false) {
            $jsOutput = $this->jsFactory->getCoreJs();
        }

        $jsOutput .= $this->jsFactory->getDashboardJs(
            $this->volcano->get('Dashboard', $label)
        );

        return $jsOutput;
    }

    /**
     * Outputs the link to the Google JSAPI
     *
     *
     * @deprecated 3.1.0 Manual script tag location output is no longer needed,
     *                   lava.js injects the script into the head.
     *
     * @access public
     * @since  2.3.0
     * @return string Google Chart API and lava.js script blocks
     */
    public function jsapi()
    {
        trigger_error('Using the jsapi() method is deprecated since lava.js injects the jsapi into the head.', E_USER_DEPRECATED);

        return $this->jsFactory->getCoreJs();
    }

    /**
     * Checks to see if the given chart or dashboard exists in the volcano storage.
     *
     * @access public
     * @since  2.4.2
     * @uses   \Khill\Lavacharts\Values\Label
     * @param  string $type Type of object to check.
     * @param  string $label Label of the object to check.
     * @return boolean
     */
    public function exists($type, $label)
    {
        $label = new Label($label);

        if ($type == 'Dashboard') {
            return $this->volcano->checkDashboard($label);
        } else {
            return $this->volcano->checkChart($type, $label);
        }
    }

    /**
     * Fetches an existing Chart or Dashboard from the volcano storage.
     *
     * @access public
     * @since  3.0.0
     * @uses   \Khill\Lavacharts\Values\Label
     * @param  string $type Type of Chart or Dashboard.
     * @param  string $label Label of the Chart or Dashboard.
     * @return mixed
     */
    public function fetch($type, $label)
    {
        return $this->volcano->get($type, $label);
    }

    /**
     * Stores a existing Chart or Dashboard into the volcano storage.
     *
     * @access public
     * @since  3.0.0
     * @param  \Khill\Lavacharts\Configs\Renderable $renderable A Chart or Dashboard.
     * @return \Khill\Lavacharts\Charts\Chart|\Khill\Lavacharts\Dashboards\Dashboard
     */
    public function store(Renderable $renderable)
    {
        return $this->volcano->store($renderable);
    }



    /**
     * Creates and stores Dashboards
     *
     * If the Dashboard is found in the Volcano, then it is returned.
     * Otherwise, a new dashboard is created and stored in the Volcano.
     *
     * @access private
     * @since  3.1.0
     * @uses   \Khill\Lavacharts\Dashboards\Dashboard
     * @param  \Khill\Lavacharts\Values\Label $label    Label of the dashboard.
     * @param  array                          $bindings Array of bindings to apply
     * @return \Khill\Lavacharts\Dashboards\Dashboard
     */
    private function dashboardFactory(Label $label, $bindings)
    {
        if ($this->volcano->checkDashboard($label) === false) {
            $dashboard = new Dashboard($label, $bindings);

            $this->volcano->store($dashboard);
        }

        return $this->volcano->get('Dashboard', $label);
    }

    /**
     * Creates Format Objects
     *
     * @access private
     * @since  2.0.0
     * @param  string $type Type of format to create.
     * @param  string $args Arguments from __call
     * @throws \Khill\Lavacharts\Exceptions\InvalidFunctionParam
     * @return \Khill\Lavacharts\DataTables\Formats\Format
     */
    private function formatFactory($type, $args)
    {
        $format = __NAMESPACE__ . '\\DataTables\\Formats\\' . $type;

        if (isset($args[0]) === false) {
            return new $format;
        }

        if (is_array($args[0]) === false || empty($args[0]) === true) {
            throw new InvalidFunctionParam(
                $args[0],
                __FUNCTION__,
                'array'
            );
        }

        return new $format($args[0]);
    }

    /**
     * Checks if running in composer environment
     *
     * This will check if the folder 'composer' is within the path to Lavacharts.
     *
     * @access private
     * @since  2.4.0
     * @return boolean
     */
    private function usingComposer()
    {
        if (strpos(realpath(__FILE__), 'composer') !== false) {
            return true;
        } else {
            return false;
        }
    }
}
