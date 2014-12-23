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

use Khill\Lavacharts\Helpers\Helpers as h;
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
    private $jsAPI = '<script type="text/javascript" src="//google.com/jsapi"></script>';

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
        $out = $this->jsO.PHP_EOL;

        //Creating new chart js object
        $out .= sprintf(
            'lava.charts.%s = {"%s":{chart:null,draw:null,data:null,options:null,formats:[]}};',
            $this->chart->type,
            $this->chart->label
        ).PHP_EOL.PHP_EOL;

        //Checking if output div exists
        $out .= sprintf(
            "if (!document.getElementById('%s'))" .
            "{console.error('[Lavaharts] No matching element was found with ID \"%s\"');}",
            $this->elementId,
            $this->elementId
        ).PHP_EOL.PHP_EOL;

        $out .= sprintf(
            "lava.charts.%s.%s.draw = function() {",
            $this->chart->type,
            $this->chart->label
        ).PHP_EOL;

        $out .= sprintf(
            'var $this = lava.charts.%s.%s;',
            $this->chart->type,
            $this->chart->label
        ).PHP_EOL.PHP_EOL;

        $out .= sprintf(
            '$this.data = new google.visualization.DataTable(%s, %s);',
            $this->chart->datatable->toJson(),
            $this->googleDataTableVer
        ).PHP_EOL.PHP_EOL;

        $out .= sprintf(
            '$this.options = %s;',
            $this->chart->optionsToJson()
        ).PHP_EOL.PHP_EOL;

        $out .= sprintf(
            '$this.chart = new google.visualization.%s',
            ($this->chart->type == 'DonutChart' ? 'PieChart' : $this->chart->type)
        );
        $out .= sprintf("(document.getElementById('%s'));", $this->elementId).PHP_EOL.PHP_EOL;

        if ($this->chart->datatable->hasFormats()) {
            $out .= $this->buildFormatters();
        }

        $out .= '$this.chart.draw($this.data, $this.options);'.PHP_EOL;

        if ($this->chart->hasEvents()) {
            $out .= $this->buildEventCallbacks();
        }

        $out .= "};".PHP_EOL.PHP_EOL;

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

        $out .= sprintf(
            "google.load('visualization', '1', {'packages':['%s']});",
            $vizType
        ).PHP_EOL;

        $out .= sprintf(
            "google.setOnLoadCallback(lava.charts.%s.%s.draw);",
            $this->chart->type,
            $this->chart->label
        ).PHP_EOL;

        $out .= sprintf(
            "onResize(function(){lava.charts.%s.%s.draw()});",
            $this->chart->type,
            $this->chart->label
        ).PHP_EOL;

        if (self::DEBUG) {
            $out .='console.debug(lava);';
        }

        $out .= $this->jsC.PHP_EOL;

        return $out;
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

            $cb = sprintf(
                'function (event) { return lava.event(event, $this.chart, %s); }',
                $event->callback
            );

            $out .= sprintf(
                'google.visualization.events.addListener($this.chart, "%s", %s);',
                $event::TYPE,
                $cb
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
                '$this.formats["col%s"].format($this.data, %s);',
                $index,
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
    public function coreJsRendered($stat = false) {
        if ($stat === false) {
            return $this->coreJsRendered;
        } else {
            return $this->coreJsRendered = $stat;
        }
    }

    /**
     * Builds the javascript lava object for chart interation.
     *
     * @access public
     *
     * @return string Javascript code block.
     */
    public function getCoreJs()
    {
        $out  = $this->jsAPI;
        $out .= $this->jsO;
        $out .=
<<<JSCORE
    function onResize(c,t){onresize=function(){clearTimeout(t);t=setTimeout(c,100)};return c};

    var lava = lava || { get:null, event:null, charts:{} };

    lava.get = function (chartLabel) {
        var chartTypes = Object.keys(lava.charts),
            chart;

        if (typeof chartLabel === 'string') {
            if (Array.isArray(chartTypes)) {
                chartTypes.some(function (e) {
                    if (typeof lava.charts[e][chartLabel] !== 'undefined') {
                        chart = lava.charts[e][chartLabel].chart;

                        return true;
                    } else {
                        return false;
                    }
                });
            } else {
                return false;
            }
        } else {
            console.error('[Lavacharts] The input for lava.get() must be a string.');

            return false;
        }
    };

    lava.event = function (event, chart, callback) {
        return callback(event, chart);
    };
JSCORE;
        $out .= $this->jsC;

        return $out;
    }

}


//{prefix: '$', negativeColor: 'red', negativeParens: true}
