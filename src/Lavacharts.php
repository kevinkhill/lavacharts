<?php

namespace Khill\Lavacharts;

use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Values\ElementId;
use \Khill\Lavacharts\Charts\Chart;
use \Khill\Lavacharts\DataTables\DataTable;
use \Khill\Lavacharts\Dashboards\Dashboard;
use \Khill\Lavacharts\Dashboards\ChartWrapper;
use \Khill\Lavacharts\Dashboards\ControlWrapper;
use \Khill\Lavacharts\Dashboards\Filters\Filter;
use \Khill\Lavacharts\Javascript\JavascriptFactory;
use \Khill\Lavacharts\Exceptions\InvalidDataTable;
use \Khill\Lavacharts\Exceptions\InvalidLabel;
use \Khill\Lavacharts\Exceptions\InvalidLavaObject;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;
use \Khill\Lavacharts\Exceptions\InvalidFilterObject;
use \Khill\Lavacharts\Exceptions\InvalidEventCallback;
use \Khill\Lavacharts\Exceptions\InvalidFunctionParam;
use \Khill\Lavacharts\Exceptions\InvalidDivDimensions;

/**
 * Lavacharts - A PHP wrapper library for the Google Chart API
 *
 *
 * @category  Class
 * @package   Lavacharts
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2015, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT MIT
 */
class Lavacharts
{
    /**
     * Holds all of the defined Charts and DataTables.
     *
     * @var Volcano
     */
    public $volcano;

    /**
     * JavascriptFactory for outputting lava.js and chart/dashboard javascript
     *
     * @var JavascriptFactory
     */
    public $jsFactory;

    /**
     * Types of charts that can be created.
     *
     * @var array
     */
    private $chartClasses = [
        'AreaChart',
        'BarChart',
        'CalendarChart',
        'ColumnChart',
        'ComboChart',
        'PieChart',
        'DonutChart',
        'GaugeChart',
        'GeoChart',
        'LineChart',
        'ScatterChart'
    ];

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
     * Types of events.
     *
     * @var array
     */
    private $eventClasses = [
        'AnimationFinish',
        'Callback',
        'Error',
        'MouseOut',
        'MouseOver',
        'Ready',
        'Select'
    ];

    /**
     * Types of filters.
     *
     * @var array
     */
    private $filterClasses = [
        'CategoryFilter',
        'ChartRangeFilter',
        'DateRangeFilter',
        'NumberRangeFilter',
        'StringFilter'
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

        $this->volcano   = new Volcano;
        $this->jsFactory = new JavascriptFactory;
    }

    /**
     * Magic function to reduce repetitive coding and create aliases.
     *
     * @access public
     * @since  1.0.0
     * @param  string $method    Name of method
     * @param  array  $arguments Passed arguments
     * @throws \Khill\Lavacharts\Exceptions\InvalidLabel
     * @throws \Khill\Lavacharts\Exceptions\InvalidLavaObject
     * @throws \Khill\Lavacharts\Exceptions\InvalidFilterObject
     * @throws \Khill\Lavacharts\Exceptions\InvalidFunctionParam
     * @return mixed Returns Charts, DataTables, and Config Objects, Events, Filters
     */
    public function __call($method, $arguments)
    {
        //Rendering Aliases
        if ((bool) preg_match('/^render/', $method) === true) {
            $type = str_replace('render', '', $method);

            if ($type !== 'Dashboard' && in_array($type, $this->chartClasses, true) === false) {
                throw new InvalidLavaObject($type);
            }

            $lavaClass = $this->render($type, $arguments[0], $arguments[1]);
        }

        //Charts
        if (in_array($method, $this->chartClasses)) {
            $lavaClass = $this->chartFactory($method, $arguments);
        }

        //Formats
        if (in_array($method, $this->formatClasses)) {
            $lavaClass = $this->formatFactory($method, $arguments);
        }

        //Events
        if (in_array($method, $this->eventClasses)) {
            $lavaClass = $this->eventFactory($method, $arguments);
        }

        //Filters
        if ((bool) preg_match('/Filter$/', $method)) {
            $lavaClass = $this->filterFactory($method, $arguments);
        }

        return $lavaClass;
    }

    /**
     * Create a new DataTable
     *
     * If the additional DataTablePlus package is available, then one will
     * be created, otherwise a standard DataTable is returned.
     *
     * @since  3.0.0
     * @param  string $timezone
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public function DataTable($timezone = null)
    {
        $datatable = '\Khill\Lavacharts\DataTablePlus\DataTablePlus';

        if (class_exists($datatable) === false) {
            $datatable = '\Khill\Lavacharts\DataTables\DataTable';
        }

        if (is_null($timezone) === false) {
            return new $datatable($timezone);
        } else {
            return new $datatable;
        }
    }

    /**
     * Create a new Dashboard
     *
     * @since  3.0.0
     * @param  string $label
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public function Dashboard($label)
    {
        $label = new Label($label);

        return $this->dashboardFactory($label);
    }

    /**
     * Create a new ControlWrapper from a Filter
     *
     * @since  3.0.0
     * @uses   \Khill\Lavacharts\Values\ElementId
     * @param  \Khill\Lavacharts\Dashboards\Filters\Filter $filter Filter to wrap
     * @param  string $elementId HTML element ID to output the control.
     * @return \Khill\Lavacharts\Dashboards\ControlWrapper
     */
    public function ControlWrapper(Filter $filter, $elementId)
    {
        $elementId = new ElementId($elementId);

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
     */
    public function ChartWrapper(Chart $chart, $elementId)
    {
        $elementId = new ElementId($elementId);

        return new ChartWrapper($chart, $elementId);
    }

    /**
     * Renders Charts or Dashboards into the page
     *
     * Given a type, label, and HTML element id, this will output
     * all of the necessary javascript to generate the chart or dashboard.
     *
     * @access public
     * @since  2.0.0
     * @uses   \Khill\Lavacharts\Values\Label
     * @uses   \Khill\Lavacharts\Values\ElementId
     * @param  string $type Type of object to render.
     * @param  string $label Label of the object to render.
     * @param  string $elementId HTML element id to render into.
     * @param  mixed  $divDimensions Set true for div creation, or pass an array with height & width
     * @return string
     */
    public function render($type, $label, $elementId, $divDimensions = false)
    {
        $label     = new Label($label);
        $elementId = new ElementId($elementId);

        if ($type == 'Dashboard') {
            $output = $this->renderDashboard($label, $elementId);
        } else {
            $output = $this->renderChart($type, $label, $elementId, $divDimensions);
        }

        return $output;
    }

    /**
     * Renders the chart into the page
     *
     * Given a chart label and an HTML element id, this will output
     * all of the necessary javascript to generate the chart.
     *
     * @access public
     * @since  3.0.0
     * @uses   \Khill\Lavacharts\Values\Label
     * @uses   \Khill\Lavacharts\Values\ElementId
     * @param  string $chartType Type of chart to render.
     * @param  \Khill\Lavacharts\Values\Label $chartLabel Label of a saved chart.
     * @param  \Khill\Lavacharts\Values\ElementId $elementId HTML element id to render the chart into.
     * @param  mixed  $divDimensions Set true for div creation, or pass an array with height & width
     * @return string
     */
    private function renderChart($type, Label $label, ElementId $elementId, $divDimensions = false)
    {
        $jsOutput = '';

        if ($this->jsFactory->coreJsRendered() === false) {
            $jsOutput = $this->jsFactory->getCoreJs();
        }

        if ($divDimensions !== false) {
            $jsOutput .= $this->div($elementId, $divDimensions);
        }

        $jsOutput .= $this->jsFactory->getChartJs(
            $this->volcano->getChart($type, $label),
            $elementId
        );

        return $jsOutput;
    }

    /**
     * Renders the chart into the page
     *
     * Given a chart label and an HTML element id, this will output
     * all of the necessary javascript to generate the chart.
     *
     * @access public
     * @since  3.0.0
     * @uses   \Khill\Lavacharts\Values\Label
     * @uses   \Khill\Lavacharts\Values\ElementId
     * @param  \Khill\Lavacharts\Values\Label $chartLabel Label of a saved chart.
     * @param  \Khill\Lavacharts\Values\ElementId $elementId HTML element id to render the chart into.
     * @return string
     */
    private function renderDashboard(Label $label, ElementId $elementId)
    {
        $jsOutput = '';

        if ($this->jsFactory->coreJsRendered() === false) {
            $jsOutput = $this->jsFactory->getCoreJs();
        }

        $jsOutput .= $this->jsFactory->getDashboardJs(
            $this->volcano->getDashboard($label),
            $elementId
        );

        return $jsOutput;
    }

    /**
     * Outputs the link to the Google JSAPI
     *
     * @access public
     * @since  2.3.0
     * @return string Google Chart API and lava.js script blocks
     */
    public function jsapi()
    {
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
        $label = new Label($label);

        if ($type == 'Dashboard') {
            return $this->volcano->getDashboard($label);
        } else {
            return $this->volcano->getChart($type, $label);
        }
    }

    /**
     * Stores a existing Chart or Dashboard into the volcano storage.
     *
     * @access public
     * @since  3.0.0
     * @param  Chart|Dashboard $lavaObj Chart or Dashboard.
     * @return boolean
     */
    public function store($lavaObj)
    {
        if ($lavaObj instanceof Dashboard) {
            return $this->volcano->storeDashboard($lavaObj);
        }

        if ($lavaObj instanceof Chart) {
            return $this->volcano->storeChart($lavaObj);
        }

        return false;
    }

    /**
     * Builds a div html element for the chart to be rendered into.
     *
     * Calling with no arguments will return a div with the ID set to what was
     * given to the outputInto() function.
     *
     * Passing two (int)s will set the width and height respectively and the div
     * ID will be set via the string given in the outputInto() function.
     *
     *
     * This is useful for the AnnotatedTimeLine Chart since it MUST have explicitly
     * defined dimensions of the div it is rendered into.
     *
     * The other charts do not require height and width, but do require an ID of
     * the div that will be receiving the chart.
     *
     * @access private
     * @since  1.0.0
     * @param  \Khill\Lavacharts\Values\ElementId $elementId  Element id to apply to the div.
     * @param  array|boolean $dimensions Height & width of the div.
     * @throws \Khill\Lavacharts\Exceptions\InvalidDivDimensions
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return string HTML div element.
     */
    private function div(ElementId $elementId, $dimensions = true)
    {
        if ($dimensions === true) {
            return sprintf('<div id="%s"></div>', $elementId);
        } else {
            if (is_array($dimensions) && ! empty($dimensions)) {
                if (array_key_exists('height', $dimensions) && array_key_exists('width', $dimensions)) {
                    $widthCheck  = (is_int($dimensions['width'])  && $dimensions['width']  > 0);
                    $heightCheck = (is_int($dimensions['height']) && $dimensions['height'] > 0);

                    if ($widthCheck && $heightCheck) {
                            return sprintf(
                                '<div id="%s" style="width:%spx; height:%spx;"></div>',
                                $elementId,
                                $dimensions['width'],
                                $dimensions['height']
                            );
                    } else {
                        throw new InvalidConfigValue(
                            __METHOD__,
                            'int',
                            'greater than 0'
                        );
                    }
                } else {
                    throw new InvalidDivDimensions();
                }
            } else {
                throw new InvalidDivDimensions();
            }
        }
    }

    /**
     * Creates and stores Charts
     *
     * If args contains a label and datatable, a chart will be created,
     * stored in the Volcano and returned.
     *
     * If args only contains a label, and the chart already exists in the
     * Volcano, then it will  be returned.
     *
     * @access private
     * @since  2.0.0
     * @uses   \Khill\Lavacharts\Values\Label
     * @param  string $type Type of chart to fetch or create.
     * @param  string $args Arguments from __call
     * @param  \Khill\Lavacharts\DataTables\DataTable $datatable DataTable used for the chart.
     * @throws \Khill\Lavacharts\Exceptions\InvalidLabel
     * @throws \Khill\Lavacharts\Exceptions\InvalidDataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidFunctionParam
     * @return \Khill\Lavacharts\Charts\Chart
     */
    private function chartFactory($type, $args)
    {
        $chart     = null;
        $datatable = null;

        if (isset($args[0]) === false) {
            throw new InvalidLabel;
        } else {
            $chartLabel = new Label($args[0]);
        }

        if ($this->volcano->checkChart($type, $chartLabel) === true) {
            return $this->volcano->getChart($type, $chartLabel);
        }

        if (isset($args[1]) === false) {
            throw new InvalidDataTable;
        }

        if ($args[1] instanceof DataTable === false) {
            throw new InvalidDataTable($args[1]);
        }

        if (isset($args[2]) === true && is_array($args[2]) === false) {
            throw new InvalidFunctionParam(
                $args[2],
                __FUNCTION__,
                'array'
            );
        }

        if ($args[1] instanceof DataTable === false) {
            throw new InvalidDataTable($args[1]);
        }

        $chartObject = __NAMESPACE__ . '\\Charts\\' . $type;

        if (isset($args[2]) === true && is_array($args[2]) === true) {
            $chart = new $chartObject($chartLabel, $args[1], $args[2]);
        } else {
            $chart = new $chartObject($chartLabel, $args[1]);
        }

        $this->volcano->storeChart($chart);

        return $chart;
    }

    /**
     * Creates and stores Dashboards
     *
     * If the Dashboard is found in the Volcano, then it is returned.
     * Otherwise, a new dashboard is created and stored in the Volcano.
     *
     * @access private
     * @since  3.0.0
     * @uses   \Khill\Lavacharts\Dashboards\Dashboard
     * @param  \Khill\Lavacharts\Values\Label $label Label of the dashboard.
     * @return \Khill\Lavacharts\Dashboards\Dashboard
     */
    private function dashboardFactory(Label $label)
    {
        if ($this->volcano->checkDashboard($label) === false) {
            $dashboard = new Dashboard($label);

            $this->volcano->storeDashboard($dashboard);
        }

        return $this->volcano->getDashboard($label);
    }

    /**
     * Creates Config Objects
     *
     * If args[0] contains an array of options then they are passed to the
     * ConfigObject. Otherwise an empty ConfigObject is created.
     *
     * @access private
     * @since  2.0.0
     * @param  string $type Type of configObject to create.
     * @param  string $args Arguments from __call
     * @throws \Khill\Lavacharts\Exceptions\InvalidFunctionParam
     * @return \Khill\Lavacharts\Configs\ConfigObject
     */
    private function configFactory($type, $args)
    {
        $configObj = __NAMESPACE__ . '\\Configs\\' . $type;

        if (isset($args[0]) === false) {
            return new $configObj;
        }

        if (is_array($args[0]) === false || empty($args[0]) === true) {
            throw new InvalidFunctionParam(
                $args[0],
                __FUNCTION__,
                'array'
            );
        }

        return new $configObj($args[0]);

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
     * Creates Event Objects
     *
     * @access private
     * @since  2.0.0
     * @param  string $type Type of event to create.
     * @param  string $args Arguments from __call
     * @throws \Khill\Lavacharts\Exceptions\InvalidEventCallback
     * @return \Khill\Lavacharts\Events\Event
     */
    private function eventFactory($type, $args)
    {
        if (isset($args[0]) === false) {
            throw new InvalidEventCallback;
        }

        if (Utils::nonEmptyString($args[0]) === false) {
            throw new InvalidEventCallback($args[0]);
        }

        $event = __NAMESPACE__ . '\\Events\\' . $type;

        return new $event($args[0]);
    }

    /**
     * Creates Filter Objects
     *
     * @access private
     * @since  3.0.0
     * @param  string $type Type of filter to create.
     * @param  string $args Arguments from __call
     * @throws \Khill\Lavacharts\Exceptions\InvalidLabel
     * @throws \Khill\Lavacharts\Exceptions\InvalidFunctionParam
     * @return \Khill\Lavacharts\Dashboards\Filters\Filter
     */
    private function filterFactory($type, $args)
    {
        if (is_string($args[0]) === false && is_int($args[0]) === false) {
            throw new InvalidConfigValue(
                static::TYPE,
                __FUNCTION__,
                'string|int'
            );
        }

        if (in_array($type, $this->filterClasses) === false) {
            throw new InvalidFilterObject(
                $type,
                $this->filterClasses
            );
        }

        $filter = __NAMESPACE__ . '\\Dashboards\\Filters\\' . str_replace('Filter', '', $type);

        if (is_array($args[1])) {
            return new $filter($args[0], $args[1]);
        } else {
            return new $filter($args[0]);
        }
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
