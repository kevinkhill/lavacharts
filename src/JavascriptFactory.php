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
        $mappedValues = [
            'type'    => $chart::TYPE,
            'label'   => $chart->label,
            'version' => $chart::VERSION,
            'data'    => $chart->getDataTableJson(),
            'options' => $chart->optionsToJson(),
            'class'   => $chart::VIZ_CLASS,
            'package' => $chart::VIZ_PACKAGE,
            'elemId'  => $this->elementId,
            'dataVer' => DataTable::VERSION
        ];
        //preg_replace(['/type/', '/version/', '/chart/'], ['Lava', '1.1', 'LineChart'], $a);
        $this->out = self::JS_OPEN.PHP_EOL;
        $this->out .=
<<<JS1
        /**
         * If the object does not exist for a given chart type, initialise it.
         * This will prevent overriding keys when multiple charts of the same
         * type are being rendered on the same page.
         */
        if ( typeof lava.charts.<type> == "undefined" ) {
            lava.charts.<type> = {};
        }

        //Creating a new lavachart object
        lava.charts.<type>["<label>"] = new lava.emptyChart();

        //Checking if output div exists
        if (! document.getElementById("<elemId>")) {
            throw new Error('[Lavacharts] No matching element was found with ID "<elemId>"');
        }

        lava.charts.<type>["<label>"].draw = function() {

            var $this = lava.charts.<type>["<label>"];

            $this.data = new google.visualization.DataTable(<data>, <dataVer>);

            $this.options = <options>;

            $this.chart = new <class>(document.getElementById("<elemId>"));
JS1;

        if ($chart->getDataTable()->hasFormats()) {
            $this->buildFormatters($chart);
        }

        if ($chart->hasEvents()) {
            $this->buildEventCallbacks($chart);
        }

        $this->out .=
<<<JS2
            $this.chart.draw($this.data, $this.options);
        };

        google.load('visualization', '<version>', {'packages':['<package>']});
        google.setOnLoadCallback(lava.charts.<type>["<label>"].draw);

        lava.register("<type>", "<label>");
JS2;

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
