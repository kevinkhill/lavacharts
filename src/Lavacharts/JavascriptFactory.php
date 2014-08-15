<?php namespace Lavacharts;

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

use Lavacharts\Helpers\Helpers as h;
use Lavacharts\Charts\Chart;
use Lavacharts\Events\Event;
use Lavacharts\Exceptions\DataTableNotFound;
use Lavacharts\Exceptions\InvalidElementId;

class JavascriptFactory
{
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
    private $googleAPI = '<script type="text/javascript" src="//google.com/jsapi"></script>';

    /**
     * Google's DataTable Version
     *
     * @var string
     */
    private $googleDataTableVer = '0.6';


    /**
     * Checks the Chart for DataTable and uilds the Javascript code block
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
        if (isset($chart->datatable) && h::isDataTable($chart->datatable)) {
            $this->chart = $chart;
        } else {
            throw new DataTableNotFound($chart);
        }

        if (h::nonEmptyString($elementId)) {
            $this->elementId = $elementId;
        } else {
            throw new InvalidElementId($elementId);
        }

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
        $out  = $this->googleAPI.PHP_EOL;
/*
        if (is_array($this->chart->events) && count($this->chart->events) > 0) {
            $out .= $this->_build_event_callbacks($this->chart->type, $this->chart->events);
        }
*/
        $out .= $this->jsO.PHP_EOL;
        $out .= 'var lavacharts = {formatters:{}};'.PHP_EOL;

        switch ($this->chart->type) {
            case 'AnnotatedTimeLine':
                $vizType = 'annotatedtimeline';
                break;

            case 'GeoChart':
                $vizType = 'geochart';
                break;

            default:
                $vizType = 'corechart';
                break;
        }

        //Checking if output div exists
        $out .= sprintf("if (!document.getElementById('%s'))", $this->elementId);
        $out .= sprintf("{console.error('[Lavacharts] No matching element was found with ID \"%s\"');}", $this->elementId).PHP_EOL;

        $out .= sprintf("google.load('visualization', '1', {'packages':['%s']});", $vizType).PHP_EOL;
        $out .= 'google.setOnLoadCallback(drawChart);'.PHP_EOL;
        $out .= 'function drawChart() {'.PHP_EOL;

        $out .= sprintf(
            'var data = new google.visualization.DataTable(%s, %s);',
            $this->chart->datatable->toJson(),
            $this->googleDataTableVer
        ).PHP_EOL;

        $out .= sprintf('var options = %s;', $this->chart->optionsToJson()).PHP_EOL;
        $out .= sprintf('var chart = new google.visualization.%s', $this->chart->type);
        $out .= sprintf("(document.getElementById('%s'));", $this->elementId).PHP_EOL;

        if ($this->chart->datatable->hasFormats()) {
            $out .= $this->buildFormatters().PHP_EOL;
        }

        $out .= 'chart.draw(data, options);'.PHP_EOL;
/*
        if (is_array($this->chart->events) && count($this->chart->events) > 0) {
            foreach ($this->chart->events as $event) {
                $out .= sprintf('google.visualization.events.addListener(chart, "%s", ', $event);
                $out .= sprintf('function (event) { %s.%s(event); });', $this->chart->type, $event).PHP_EOL;
            }
        }
*/

        $out .= "}".PHP_EOL;
        $out .= $this->jsC.PHP_EOL;

        return $out;
    }

    /**
     * Builds the javascript object of event callbacks.
     *
     * @access private
     * @param string $chartType.
     * @param array  $chartEvents Array of events to apply to the chart.
     *
     * @return string Javascript code block.
     */
    private function buildEventCallbacks($chartType, $chartEvents)
    {
        $script = sprintf('if (typeof %s !== "object") { %s = {}; }', $chartType, $chartType).PHP_EOL.PHP_EOL;

        foreach ($chartEvents as $event) {
             $script .= sprintf('%s.%s = function (event) {', $chartType, $event).PHP_EOL;

             $callback = $this->callbackPath.$chartType.'.'.$event.'.js';
             $callbackScript = file_get_contents($callback);

            if ($callbackScript !== false) {
                $script .= $callbackScript.PHP_EOL;
            } else {
                $this->setError(__METHOD__, 'Error loading javascript file, in '.$callback.'.js');
            }

             $script .= "};".PHP_EOL;
        }

        $out  = $this->jsO.PHP_EOL;
        $out .= $script;
        $out .= $this->jsC.PHP_EOL;

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
                'lavacharts.formatters.col%s = new google.visualization.%s(%s);',
                $index,
                $format::TYPE,
                $format->toJson()
            ).PHP_EOL;

            $out .= sprintf(
                'lavacharts.formatters.col%s.format(data, %s);',
                $index,
                $index
            ).PHP_EOL;
        }

        return $out;
    }
}


//{prefix: '$', negativeColor: 'red', negativeParens: true}
