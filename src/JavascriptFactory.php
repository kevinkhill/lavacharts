<?php namespace Khill\Lavacharts;

/**
 * JavascriptFactory Class
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

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Charts\Chart;
use \Khill\Lavacharts\Events\Event;
use \Khill\Lavacharts\Configs\DataTable;
use \Khill\Lavacharts\Exceptions\InvalidElementId;

class JavascriptFactory
{
    const DEBUG = false;

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
     * True if the lava object and jsapi have been added to the page.
     *
     * @access private
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
     * @return string Javascript code blocks.
     */
    public function getCoreJs()
    {
        $coreJs  = self::JSAPI;
        $coreJs .= self::JS_OPEN;
        $coreJs .= file_get_contents(__DIR__.'/../javascript/lava.js');
        $coreJs .= self::JS_CLOSE;

        return $coreJs;
    }

    /**
     * Checks for an element id to output the chart into and builds the Javascript.
     *
     * @access public
     * @uses   Chart
     * @param  Chart             $chart     Chart object to render.
     * @param  string            $elementId HTML element id to output the chart into.
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
     * Builds the Javascript code block
     *
     * Build the script block for the chart. If there are any events defined,
     * they will be automatically be attached to the chart and
     * pulled from the callbacks folder.
     *
     * @access private
     * @param  Chart  $chart
     * @return string Javascript code block.
     */
    private function buildChartJs(Chart $chart)
    {
        $this->out = self::JS_OPEN.PHP_EOL;

        /*
         *  If the object does not exist for a given chart type, initialise it.
         *  This will prevent overriding keys when multiple charts of the same
         *  type are being rendered on the same page.
         */
        $this->out .= sprintf(
            'if ( typeof lava.charts.%1$s == "undefined" ) { lava.charts.%1$s = {}; }',
            $chart::TYPE
        ).PHP_EOL.PHP_EOL;

        //Creating new chart js object
        $this->out .= sprintf(
            'lava.charts.%s["%s"] = {chart:null,draw:null,data:null,options:null,formats:[]};',
            $chart::TYPE,
            $chart->label
        ).PHP_EOL.PHP_EOL;

        //Checking if output div exists
        $this->out .= sprintf(
            'if (!document.getElementById("%1$s"))' .
            '{console.error("[Lavacharts] No matching element was found with ID \"%1$s\"");}',
            $this->elementId
        ).PHP_EOL.PHP_EOL;

        $this->out .= sprintf(
            'lava.charts.%s["%s"].draw = function() {',
            $chart::TYPE,
            $chart->label
        ).PHP_EOL;

        $this->out .= sprintf(
            'var $this = lava.charts.%s["%s"];',
            $chart::TYPE,
            $chart->label
        ).PHP_EOL.PHP_EOL;

        $this->out .= sprintf(
            '$this.data = new google.visualization.DataTable(%s, %s);',
            $chart->getDataTableJson(),
            DataTable::VERSION
        ).PHP_EOL.PHP_EOL;

        $this->out .= sprintf(
            '$this.options = %s;',
            $chart->optionsToJson()
        ).PHP_EOL.PHP_EOL;

        $this->out .= sprintf(
            '$this.chart = new %s(document.getElementById("%s"));',
            $chart::VIZ_CLASS,
            $this->elementId
        ).PHP_EOL.PHP_EOL;

        if ($chart->getDataTable()->hasFormats()) {
            $this->buildFormatters($chart);
        }

        if ($chart->hasEvents()) {
            $this->buildEventCallbacks($chart);
        }

        $this->out .= '$this.chart.draw($this.data, $this.options);'.PHP_EOL;

        $this->out .= "};".PHP_EOL.PHP_EOL;

        $this->out .= sprintf(
            "google.load('visualization', '%s', {'packages':['%s']});",
            $chart::VERSION,
            $chart::VIZ_PACKAGE
        ).PHP_EOL;

        $this->out .= sprintf(
            'google.setOnLoadCallback(lava.charts.%s["%s"].draw);',
            $chart::TYPE,
            $chart->label
        ).PHP_EOL;

        $this->out .= sprintf(
            'lava.register("%s", "%s");',
            $chart::TYPE,
            $chart->label
        ).PHP_EOL;

        if (self::DEBUG) {
            $this->out .= 'console.debug(lava);';
        }

        $this->out .= self::JS_CLOSE.PHP_EOL;

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
        foreach ($chart->getEvents() as $event) {
            $callback = sprintf(
                'function (event) {return lava.event(event, $this.chart, %s);}',
                $event->callback
            );

            $this->out .= sprintf(
                'google.visualization.events.addListener($this.chart, "%s", %s);',
                $event::TYPE,
                $callback
            ).PHP_EOL.PHP_EOL;

        }
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
        foreach ($chart->getDataTable()->getFormats() as $index => $format) {
            $this->out .= sprintf(
                '$this.formats["col%s"] = new google.visualization.%s(%s);',
                $index,
                $format::TYPE,
                $format->toJson()
            ).PHP_EOL;

            $this->out .= sprintf(
                '$this.formats["col%1$s"].format($this.data, %1$s);',
                $index
            ).PHP_EOL.PHP_EOL;
        }
    }
}
