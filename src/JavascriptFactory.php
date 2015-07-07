<?php namespace Khill\Lavacharts;

/**
 * JavascriptFactory Object
 *
 * This class takes charts and uses all the info to build the complete
 * javascript blocks for outputing into the page.
 *
 * @category  Class
 * @package   Lavacharts
 * @since     v2.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2014, KHill Designs
 * @link      http://github.com/kevinkhill/Lavacharts GitHub Repository Page
 * @link      http://kevinkhill.github.io/Lavacharts  GitHub Project Page
 * @license   http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Utils;
use Khill\Lavacharts\Charts\Chart;
use Khill\Lavacharts\Events\Event;
use Khill\Lavacharts\Exceptions\DataTableNotFound;
use Khill\Lavacharts\Exceptions\InvalidElementId;

class JavascriptFactory
{
    const DEBUG = false;

    /**
     * Chart used to generate output.
     *
     * @var Chart
     */
    private $chart;

    /**
     * HTML element id to output the chart into.
     *
     * @var string
     */
    private $elementId;

    /**
     * Event used to generate output.
     *
     * @var Event
     */
    private $event;

    /**
     * Tracks if the lava js core and jsapi have been rendered.
     *
     * @var bool
     */
    private $coreJsRendered = false;

    /**
     * Opening javascript tag.
     *
     * @var string
     */
    private $jsO = '<script type="text/javascript">';

    /**
     * Closing javascript tag.
     *
     * @var string
     */
    private $jsC = '</script>';

    /**
     * Javscript block with a link to Google's Chart API.
     *
     * @var string
     */
    private $jsAPI = '<script type="text/javascript" src="//www.google.com/jsapi"></script>';

    /**
     * Google's DataTable Version
     *
     * @var string
     */
    private $googleDataTableVer = '0.6';


    /**
     * Checks the Chart for DataTable and builds the Javascript code block
     *
     * Build the script block for the actual chart and passes it back to
     * output function of the calling chart object. If there are any
     * events defined, they will be automatically be attached to the chart and
     * pulled from the callbacks folder.
     *
     * @access public
     *
     * @uses   Chart
     * @param  Chart             $chart     Chart object to render.
     * @param  string            $elementId HTML element id to output the chart into.
     * @throws DataTableNotFound
     * @throws InvalidElementId
     *
     * @return string Javascript code block.
     */
    public function getChartJs(Chart $chart, $elementId = null)
    {
        if (isset($chart->datatable) === false) {
            throw new DataTableNotFound($chart);
        }

        if (Utils::nonEmptyString($elementId) === false) {
            throw new InvalidElementId($elementId);
        }

        $this->chart     = $chart;
        $this->elementId = $elementId;

        return $this->buildChartJs();
    }

    /**
     * Builds the Javascript code block
     *
     * Build the script block for the chart. If there are any events defined,
     * they will be automatically be attached to the chart and
     * pulled from the callbacks folder.
     *
     * @access private
     *
     * @return string Javascript code block.
     */
    private function buildChartJs()
    {
        $out = $this->jsO.PHP_EOL;

        /*
         *  If the object does not exist for a given chart type, initialise it.
         *  This will prevent overriding keys when multiple charts of the same
         *  type are being rendered on the same page.
         */
        $out .= sprintf(
            'if ( typeof lava.charts.%1$s == "undefined" ) { lava.charts.%1$s = {}; }',
            $this->chart->type
        ).PHP_EOL.PHP_EOL;

        //Creating new chart js object
        $out .= sprintf(
            'lava.charts.%s["%s"] = new lava.Chart;',
            $this->chart->type,
            $this->chart->label
        ).PHP_EOL.PHP_EOL;

        //Checking if output div exists
        $out .= sprintf(
            'if (!document.getElementById("%1$s"))' .
            '{console.error("[Lavacharts] No matching element was found with ID \"%1$s\"");}',
            $this->elementId
        ).PHP_EOL.PHP_EOL;

        $out .= sprintf(
            'lava.charts.%s["%s"].init = function() {',
            $this->chart->type,
            $this->chart->label
        ).PHP_EOL;

        $out .= sprintf(
            'var $this = lava.charts.%s["%s"];',
            $this->chart->type,
            $this->chart->label
        ).PHP_EOL.PHP_EOL;

        $out .= sprintf(
            '$this.data = new google.visualization.DataTable(%s, %s);',
            $this->chart->datatable->toJson(),
            $this->googleDataTableVer
        ).PHP_EOL;

        $out .= sprintf(
            '$this.options = %s;',
            $this->chart->optionsToJson()
        ).PHP_EOL.PHP_EOL;

        $out .= sprintf(
            '$this.chart = new google.visualization.%s(document.getElementById("%s"));',
            $this->getChartPackageData('jsObj'),
            $this->elementId
        ).PHP_EOL.PHP_EOL;

        if ($this->chart->datatable->hasFormats()) {
            $out .= $this->buildFormatters();
        }

        if ($this->chart->hasEvents()) {
            $out .= $this->buildEventCallbacks();
        }

        $out .= '$this.chart.draw($this.data, $this.options);'.PHP_EOL;

        $out .= "};".PHP_EOL.PHP_EOL;


        $out .= sprintf(
            'lava.charts.%1$s["%2$s"].setData = function (data) {' .
            '    var $this = lava.charts.%1$s["%2$s"];',
            $this->chart->type,
            $this->chart->label
        ).PHP_EOL;

        $out .= sprintf(
            '$this.data = new google.visualization.DataTable(data, %s);',
            $this->googleDataTableVer
        ).PHP_EOL;

        $out .= "};".PHP_EOL.PHP_EOL;

        $out .= sprintf(
            'lava.charts.%1$s["%2$s"].redraw = function () {' .
            '    var $this = lava.charts.%1$s["%2$s"];' .
            '    $this.chart.draw($this.data, $this.options);' .
            '};',
            $this->chart->type,
            $this->chart->label
        ).PHP_EOL;

        $out .= sprintf(
            "google.load('visualization', '%s', {'packages':['%s']});",
            $this->getChartPackageData('version'),
            $this->getChartPackageData('type')
        ).PHP_EOL;

        $out .= sprintf(
            'google.setOnLoadCallback(lava.charts.%s["%s"].init);',
            $this->chart->type,
            $this->chart->label
        ).PHP_EOL;

        $out .= sprintf(
            'lava.register("%s", "%s");',
            $this->chart->type,
            $this->chart->label
        ).PHP_EOL.PHP_EOL;

        $out .= $this->jsC.PHP_EOL;

        return $out;
    }

    /**
     * Returns the Google chart package data.
     *
     * @access private
     * @param  string $which
     *
     * @return stdClass chart package, version, and type
     */
    private function getChartPackageData($which)
    {
        $package = array();

        switch ($this->chart->type) {
            case 'AnnotatedTimeLine':
                $package['type']    = 'annotatedtimeline';
                $package['jsObj']   = $this->chart->type;
                $package['version'] = '1';
                break;

            case 'GeoChart':
                $package['type']    = 'geochart';
                $package['jsObj']   = $this->chart->type;
                $package['version'] = '1';
                break;

            case 'DonutChart':
                $package['type']    = 'corechart';
                $package['jsObj']   = 'PieChart';
                $package['version'] = '1';
                break;

            case 'CalendarChart':
                $package['type']    = 'calendar';
                $package['jsObj']   = 'Calendar';
                $package['version'] = '1.1';
                break;

            case 'GaugeChart':
                $package['type']    = 'gauge';
                $package['jsObj']   = 'Gauge';
                $package['version'] = '1';
                break;

            default:
                $package['type']    = 'corechart';
                $package['jsObj']   = $this->chart->type;
                $package['version'] = '1';
                break;
        }

        return $package[$which];
    }

    /**
     * Builds the javascript object of event callbacks.
     *
     * @access private
     *
     * @return string Javascript code block.
     */
    private function buildEventCallbacks()
    {
        $out = '';

        foreach ($this->chart->getEvents() as $event) {
            $callback = sprintf(
                'function (event) {return lava.event(event, $this.chart, %s);}',
                $event->callback
            );

            $out .= sprintf(
                'google.visualization.events.addListener($this.chart, "%s", %s);',
                $event::TYPE,
                $callback
            ).PHP_EOL.PHP_EOL;

        }

        return $out;
    }


    /**
     * Builds the javascript for the datatable column formatters.
     *
     * @access private
     *
     * @return string Javascript code block.
     */
    private function buildFormatters()
    {
        $out = '';

        foreach ($this->chart->datatable->getFormats() as $index => $format) {
            $out .= sprintf(
                '$this.formats["col%s"] = new google.visualization.%s(%s);',
                $index,
                $format::TYPE,
                $format->toJson()
            ).PHP_EOL;

            $out .= sprintf(
                '$this.formats["col%1$s"].format($this.data, %1$s);',
                $index
            ).PHP_EOL.PHP_EOL;
        }

        return $out;
    }

    /**
     * True if the lava object and jsapi have been added to the page.
     *
     * @access private
     *
     * @return bool
     */
    public function coreJsRendered($stat = false)
    {
        if ($stat !== false) {
            $this->coreJsRendered = $stat;
        }

        return $this->coreJsRendered;
    }

    /**
     * Builds the javascript lava object for chart interation.
     *
     * @access public
     *
     * @return string Javascript code blocks.
     */
    public function getCoreJs()
    {
        $out  = $this->jsAPI;
        $out .= $this->jsO;
        $out .= file_get_contents(__DIR__.'/../javascript/lava.js');
        $out .= $this->jsC;

        return $out;
    }
}
