<?php namespace Khill\Lavacharts;

/**
 * JavascriptFactory Object
 *
 * This class takes charts and uses all the info to build the complete
 * javascript blocks for outputing into the page.
 *
 * @category  Class
 * @package   Khill\Lavacharts
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2014, KHill Designs
 * @link      https://github.com/kevinkhill/LavaCharts GitHub Repository Page
 * @link      http://kevinkhill.github.io/LavaCharts/ GitHub Project Page
 * @license   http://opensource.org/licenses/MIT MIT
 */

//use Khill\Lavacharts\Volcano;
use Khill\Lavacharts\Charts\Chart;
use Khill\Lavacharts\Exceptions\InvalidLavaObject;
use Khill\Lavacharts\Exceptions\InvalidConfigValue;
use Khill\Lavacharts\Exceptions\InvalidConfigProperty;

class JavascriptFactory
{
    /**
     * @var Khill\Lavacharts\Charts\Chart Chart to used to generate output.
     */
    private $chart = null;

    /**
     * @var Khill\Lavacharts\Configs\DataTable Datatable to use for chart.
     */
    private $dataTable = null;

    /**
     * @var string Holds the javscript to be output into the browser.
     */
    private $output = null;

    /**
     * @var string Opening javascript tag.
     */
    private $jsO = '<script type="text/javascript">';

    /**
     * @var string Closing javascript tag.
     */
    private $jsC = '</script>';

    /**
     * @var string Javscript block with a link to Google's Chart API.
     */
    private $googleAPI = '<script type="text/javascript" src="//google.com/jsapi"></script>';

    /**
     * @var string Version of Google's DataTable.
     */
    private $googleDataTableVer = '0.6';

    /**
     * Builds the Javascript code block
     *
     * Build the script block for the actual chart and passes it back to
     * output function of the calling chart object. If there are any
     * events defined, they will be automatically be attached to the chart and
     * pulled from the callbacks folder.
     *
     * @access public
     * @param  Khill\Lavacharts\Volcano $volcano
     * @param  string $label Label of the chart and datatable to use.
     * @return string Javascript code block.
     */
    public function __construct(Chart $chart)//Volcano $volcano, $label)
    {
        $this->chart = $chart;//$volcano->getChart($label);
        //$this->dataTable = $volcano->getDataTable($label);
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
    public function useElementId($elementId)
    {
        if (is_string($elementId) && ! empty($elementId)) {
            $this->elementId = $elementId;
        } else {
            throw new InvalidElementId($elementId);
        }
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
    public function buildOutput()
    {
        $out = $this->googleAPI.PHP_EOL;
/*
        if(is_array($this->chart->events) && count($this->chart->events) > 0)
        {
            $out .= $this->_build_event_callbacks($this->chart->type, $this->chart->events);
        }
*/
        $out .= $this->jsO.PHP_EOL;

        switch($this->chart->type)
        {
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

        $out .= sprintf("google.load('visualization', '1', {'packages':['%s']});", $vizType).PHP_EOL;
        $out .= 'google.setOnLoadCallback(drawChart);'.PHP_EOL;
        $out .= 'function drawChart() {'.PHP_EOL;

        $out .= sprintf(
            'var data = new google.visualization.DataTable(%s, %s);',
            $this->chart->dataTable->toJSON(),
            $this->googleDataTableVer
        ).PHP_EOL;

        $out .= sprintf('var options = %s;', $this->chart->optionsToJSON()).PHP_EOL;
        $out .= sprintf('var chart = new google.visualization.%s', $this->chart->type);

        if (isset($this->chart->elementId)) {
            $htmlElementId = $this->chart->elementId;
        } else {
            $htmlElementId = $this->elementId;
        }

        $out .= sprintf("(document.getElementById('%s'));", $htmlElementId).PHP_EOL;
        $out .= 'chart.draw(data, options);'.PHP_EOL;
/*
        if(is_array($this->chart->events) && count($this->chart->events) > 0)
        {
            foreach($this->chart->events as $event)
            {
                $out .= sprintf('google.visualization.events.addListener(chart, "%s", ', $event);
                $out .= sprintf('function(event) { %s.%s(event); });', $this->chart->type, $event).PHP_EOL;
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
     * @param array $chartEvents Array of events to apply to the chart.
     *
     * @return string Javascript code block.
     */
    private function buildEventCallbacks($chartType, $chartEvents)
    {
        $script = sprintf('if(typeof %s !== "object") { %s = {}; }', $chartType, $chartType).PHP_EOL.PHP_EOL;

        foreach ($chartEvents as $event) {
             $script .= sprintf('%s.%s = function(event) {', $chartType, $event).PHP_EOL;

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
}
