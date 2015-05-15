<?php

namespace Khill\Lavacharts;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Charts\Chart;
use \Khill\Lavacharts\Events\Event;
use \Khill\Lavacharts\Configs\DataTable;
use \Khill\Lavacharts\Dashboard\Dashboard;
use \Khill\Lavacharts\Dashboard\ControlWrapper;
use \Khill\Lavacharts\Exceptions\InvalidElementId;

/**
 * JavascriptFactory Class
 *
 * This class takes charts and uses all the info to build the complete
 * javascript blocks for outputing into the page.
 *
 * @category  Class
 * @package   Lavacharts
 * @since     2.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2014, KHill Designs
 * @link      http://github.com/kevinkhill/Lavacharts GitHub Repository Page
 * @link      http://kevinkhill.github.io/Lavacharts  GitHub Project Page
 * @license   http://opensource.org/licenses/MIT MIT
 */
class JavascriptFactory
{
    /**
     * Opening javascript tag.
     *
     * @var string
     */
    const JS_OPEN = '<script type="text/javascript">';

    /**
     * Closing javascript tag.
     *
     * @var string
     */
    const JS_CLOSE = '</script>';

    /**
     * Script block for the Google's Chart API.
     *
     * @var string
     */
    const JSAPI = '<script type="text/javascript" src="//www.google.com/jsapi"></script>';

    /**
     * Javascript output.
     *
     * @var string
     */
    private $out;

    /**
     * HTML element id to output the chart into.
     *
     * @var string
     */
    private $elementId;

    /**
     * Tracks if the lava js core and jsapi have been rendered.
     *
     * @var bool
     */
    private $coreJsRendered = false;

    /**
     * Returns true|false depending on if the jsapi & lava.js core
     * have been added to the output.
     *
     * @access public
     * @return boolean
     */
    public function coreJsRendered()
    {
        return $this->coreJsRendered;
    }

    /**
     * Gets the Google chart api and lava.js core.
     *
     * @access public
     * @return string Javascript code blocks.
     */
    public function getCoreJs()
    {
        $coreJs  = self::JSAPI;
        $coreJs .= self::JS_OPEN;
        $coreJs .= file_get_contents(__DIR__.'/../javascript/lava.js');
        $coreJs .= self::JS_CLOSE;

        $this->coreJsRendered = true;

        return $coreJs;
    }

    /**
     * Checks for an element id to output the chart into and builds the Javascript.
     *
     * @access public
     * @uses   \Khill\Lavacharts\Charts\Chart
     * @param  \Khill\Lavacharts\Charts\Chart $chart Chart to render.
     * @param  string $elementId HTML element id to output the chart into.
     * @throws InvalidElementId
     * @return string Javascript code block.
     */
    public function getChartJs(Chart $chart, $elementId = null)
    {
        if (Utils::nonEmptyString($elementId) === false) {
            throw new InvalidElementId($elementId);
        }

        $this->elementId = $elementId;

        return $this->buildChartJs($chart);
    }

    /**
     * Checks for an element id to output the chart into and builds the Javascript.
     *
     * @since  3.0.0
     * @access public
     * @uses   \Khill\Lavacharts\Dashboard\Dashboard
     * @param  \Khill\Lavacharts\Dashboard\Dashboard $dashboard Dashboard to render.
     * @param  string $elementId HTML element id to output the dashboard into.
     * @throws InvalidElementId
     * @return string Javascript code block.
     */
    public function getDashboardJs(Dashboard $dashboard, $elementId = null)
    {
        if (Utils::nonEmptyString($elementId) === false) {
            throw new InvalidElementId($elementId);
        }

        $this->elementId = $elementId;

        return $this->buildDashboardJs($dashboard);
    }

    /**
     * Builds the Javascript code block for a chart.
     *
     * @access private
     * @param  Chart  $chart
     * @return string Javascript code block.
     */
    private function buildChartJs(Chart $chart)
    {
        $mappedValues = [
            'chartLabel'   => $chart->label,
            'chartType'    => $chart::TYPE,
            'chartVer'     => $chart::VERSION,
            'chartClass'   => $chart::VIZ_CLASS,
            'chartPackage' => $chart::VIZ_PACKAGE,
            'chartData'    => $chart->getDataTableJson(),
            'chartOptions' => $chart->optionsToJson(),
            'elemId'       => $this->elementId,
            'dataVer'      => DataTable::VERSION,
            'dataClass'    => DataTable::VIZ_CLASS,
            'formats'      => '',
            'events'       => ''
        ];

        if ($chart->getDataTable()->hasFormats()) {
            $mappedValues['formats'] = $this->buildFormatters($chart);
        }

        if ($chart->hasEvents()) {
            $mappedValues['events'] = $this->buildEventCallbacks($chart);
        }

        $this->out  = self::JS_OPEN.PHP_EOL;
        $this->out .=
<<<CHART
        /**
         * If the object does not exist for a given chart type, initialize it.
         * This will prevent overriding keys when multiple charts of the same
         * type are being rendered on the same page.
         */
        if ( typeof lava.charts.<chartType> == "undefined" ) {
            lava.charts.<chartType> = {};
        }

        //Creating a new lavachart object
        lava.charts.<chartType>["<chartLabel>"] = new lava.Chart();

        //Checking if output div exists
        if (! document.getElementById("<elemId>")) {
            throw new Error('[Lavacharts] No matching element was found with ID "<elemId>"');
        }

        lava.charts.<chartType>["<chartLabel>"].render = function() {
            var Chart = lava.charts.<chartType>["<chartLabel>"];

            Chart.data = new <dataClass>(<chartData>, <dataVer>);

            Chart.options = <chartOptions>;

            Chart.chart = new <chartClass>(document.getElementById("<elemId>"));

            <formats>
            <events>

            Chart.chart.draw(Chart.data, Chart.options);
        };

        lava.registerChart("<chartType>", "<chartLabel>");

        google.load('visualization', '<version>', {'packages':['<chartPackage>']});
        google.setOnLoadCallback(function() {
            lava.charts.<chartType>["<chartLabel>"].render();
            lava.readyCallback();
        );
CHART;
        $this->out .= PHP_EOL.self::JS_CLOSE;

        foreach ($mappedValues as $key => $value) {
            $this->out = preg_replace("/<$key>/", $value, $this->out);
        }

        return $this->out;
    }

    /**
     * Builds the javascript object of event callbacks.
     *
     * @access private
     * @param  Chart  $chart
     * @return string Javascript code block.
     */
    private function buildEventCallbacks(Chart $chart)
    {
        $output = '';

        foreach ($chart->getEvents() as $event) {
            $callback = sprintf(
                'function (event) {return lava.event(event, lavachart.chart, %s);}',
                $event->callback
            );

            $output .= sprintf(
                'google.visualization.events.addListener(lavachart.chart, "%s", %s);',
                $event::TYPE,
                $callback
            ).PHP_EOL.PHP_EOL;
        }

        return $output;
    }

    /**
     * Builds the javascript for the datatable column formatters.
     *
     * @access private
     * @param  Chart  $chart
     * @return string Javascript code block.
     */
    private function buildFormatters(Chart $chart)
    {
        $output = '';

        foreach ($chart->getDataTable()->getFormats() as $index => $format) {
            $output .= sprintf(
                'lavachart.formats["col%s"] = new google.visualization.%s(%s);',
                $index,
                $format::TYPE,
                $format->toJson()
            ).PHP_EOL;

            $output .= sprintf(
                'lavachart.formats["col%1$s"].format(lavachart.data, %1$s);',
                $index
            ).PHP_EOL.PHP_EOL;
        }

        return $output;
    }

    /**
     * Builds the Javascript code block for a Dashboard
     *
     * @since  3.0.0
     * @access private
     * @param  \Khill\Lavacharts\Dashbaord\Dashboard $dashboard
     * @return string Javascript code block.
     */
    private function buildDashboardJs(Dashboard $dashboard)
    {
        $a = $this->processBindings($dashboard);

        $boundChart = $dashboard->getBinding('MyPie')->getChartWrapper()->getChart();

        $mappedValues = [
            'label'     => $dashboard->label,
            'version'   => $dashboard::VERSION,
            'class'     => $dashboard::VIZ_CLASS,
            'packages'  => json_encode([
                $dashboard::VIZ_PACKAGE,
                $boundChart::VIZ_PACKAGE
            ]),
            'chartData' => $boundChart->getDataTableJson(),
            'elemId'    => $this->elementId,
            'bindings'  => $this->processBindings($dashboard),
            'dataVer'   => DataTable::VERSION,
            'dataClass' => DataTable::VIZ_CLASS,
        ];

        $this->out  = self::JS_OPEN.PHP_EOL;
        $this->out .=
<<<DASH
        //Checking if output div exists
        if (! document.getElementById("<elemId>")) {
            throw new Error('[Lavacharts] No matching element was found with ID "<elemId>"');
        }

        lava.dashboards["<label>"] = new lava.Dashboard();
        
        lava.dashboards["<label>"].render = function() {
            var Dash = lava.dashboards["<label>"];

            Dash.dashboard = new <class>(document.getElementById('<elemId>'));

            Dash.data = new <dataClass>(<chartData>, <dataVer>);

            <bindings>

            Dash.dashboard.draw(Dash.data);
        };

        google.load('visualization', '<version>', {'packages':<packages>});
        google.setOnLoadCallback(function() {
            lava.dashboards["<label>"].render();
            lava.readyCallback();
        });

        //lava.register("<chartType>", "<chartLabel>");
DASH;
        $this->out .= PHP_EOL.self::JS_CLOSE;

        foreach ($mappedValues as $key => $value) {
            $this->out = preg_replace("/<$key>/", $value, $this->out);
        }

        return $this->out;
    }

    /**
     * Process all the bindings for a Dashboard.
     *
     * Turns the chart and control wrappers into new Google Vizualization Objects.
     *
     * @since  3.0.0
     * @access public
     * @param  \Khill\Lavacharts\Dashboard\Dashboard $dashboard
     * @return string
     */
    public function processBindings(Dashboard $dashboard)
    {
        $output = '';

        foreach ($dashboard->getBindings() as $binding) {
            $chartWrapper   = $binding->getChartWrapper();
            $controlWrapper = $binding->getControlWrapper();

            $control = sprintf('"control": new %s(%s)', $controlWrapper::VIZ_CLASS, $controlWrapper->toJson());
            $chart   = sprintf('"chart"  : new %s(%s)', $chartWrapper::VIZ_CLASS,   $chartWrapper->toJson());

            $output .= sprintf('Dash.bindings["%s"] = {%s, %s};',
                                  $binding->getLabel(),
                                  $control,
                                  $chart
                              ).PHP_EOL;

            $output .= sprintf('            '.
                'Dash.dashboard.bind(Dash.bindings["%1$s"]["control"], Dash.bindings["%1$s"]["chart"]);',
                $binding->getLabel()
            );
        }

        return $output;
    }
}

