<?php namespace Khill\Lavacharts;

/**
 * JavascriptBuilder Object
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
 * @license   http://opensource.org/licenses/GPL-3.0 GPLv3
 */

use Khill\Lavacharts\Configs\JsDate;
use Khill\Lavacharts\Configs\TextStyle;
use Khill\Lavacharts\Exceptions\InvalidLavaObject;
use Khill\Lavacharts\Exceptions\InvalidConfigValue;
use Khill\Lavacharts\Exceptions\InvalidConfigProperty;

class JavascriptBuilder
{
    /**
     * @var string Holds the javscript to be output into the browser.
     */
    private $output = null;

    /**
     * @var Khill\Lavacharts\Chart Holds the chart object being used.
     */
    private $chart = null;

    /**
     * @var string Version of Google's DataTable.
     */
    private $dataTableVersion = '0.6';

    /**
     * @var string Opening javascript tag.
     */
    private $jsOpen = '<script type="text/javascript">';

    /**
     * @var string Closing javascript tag.
     */
    private $jsClose = '</script>';

    /**
     * @var string Javscript block with a link to Google's Chart API.
     */
    private $googleAPI = '<script type="text/javascript" src="//google.com/jsapi"></script>';

    /**
     * @var string Lavacharts root namespace
     */
    private $rootSpace = 'Khill\\Lavacharts\\';

    /**
     * @var array Holds all of the defined Charts.
     */
    private $chartsAndTables = array();

    /**
     * @var array Types of charts that can be created.
     */
    private $chartClasses = array(
        'DataTable',
        'LineChart',
        'AreaChart',
        'PieChart',
        'DonutChart',
        'ColumnChart',
        'GeoChart',
        'ComboChart' // @TODO WIP
    );

    /**
     * @var array Holds all of the defined configuration class names.
     */
    private $configClasses = array(
        'ConfigOptions',
        'Annotation',
        'Axis',
//        'DataCell',
        'BoxStyle',
        'BackgroundColor',
        'ChartArea',
        'ColorAxis',
        'HorizontalAxis',
        'JsDate',
        'Gradient',
        'Legend',
        'MagnifyingGlass',
        'TextStyle',
        'Tooltip',
        'Series', // @TODO WIP
        'SizeAxis',
        'Slice',
        'VerticalAxis'
    );

    /**
     * @var array Acceptable global configuration options.
     */
    private $validGlobals = array(
        'errorWrap',
        'errorClass',
        'textStyle'
    );

    /**
     * Builds the Javascript code block
     *
     * Build the script block for the actual chart and passes it back to
     * output function of the calling chart object. If there are any
     * events defined, they will be automatically be attached to the chart and
     * pulled from the callbacks folder.
     *
     * @param Khill\Lavacharts\Chart $chart
     *
     * @return string Javascript code block.
     */
    public function __construct(Chart $chart)
    {
        $this->chart = $chart;

        $out = $this->$googleAPI.PHP_EOL;

        //        if(is_array($this->chart->events) && count($this->chart->events) > 0)
        //        {
        //            $out .= $this->_build_event_callbacks($this->chart->chartType, $this->chart->events);
        //        }

        $out .= $this->$jsOpen.PHP_EOL;

        if ($this->chart->elementID == null) {
            $out .= 'alert("Error calling '.$this->chart->chartType.'(\''.$this->chart->chartLabel.'\')->outputInto(), requires a valid html elementID.");'.PHP_EOL;
        }

        if (
            isset($this->chart->data) === false &&
            isset($this->$chartsAndTables['DataTable'][$this->chart->dataTable]) === false
        ) {
            $out .= 'alert("No DataTable has been defined for '.$this->chart->chartType.'(\''.$this->chart->chartLabel.'\').");'.PHP_EOL;
        }

        switch($this->chart->chartType)
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

        if (isset($this->chart->data) && $this->chart->dataTable == 'local') {
            $out .= sprintf(
                'var data = new google.visualization.DataTable(%s, %s);',
                $this->chart->data->toJSON(),
                $this->$dataTableVersion
            ).PHP_EOL;
        }

        if (isset($this->$chartsAndTables['DataTable'][$this->chart->dataTable])) {
            $out .= sprintf(
                'var data = new google.visualization.DataTable(%s, %s);',
                $this->$chartsAndTables['DataTable'][$this->chart->dataTable]->toJSON(),
                $this->$dataTableVersion
            ).PHP_EOL;
        }

        $out .= sprintf('var options = %s;', $this->chart->optionsToJSON()).PHP_EOL;
        $out .= sprintf('var chart = new google.visualization.%s', $this->chart->chartType);
        $out .= sprintf("(document.getElementById('%s'));", $this->chart->elementID).PHP_EOL;
        $out .= 'chart.draw(data, options);'.PHP_EOL;

        //        if(is_array($this->chart->events) && count($this->chart->events) > 0)
        //        {
        //            foreach($this->chart->events as $event)
        //            {
        //                $out .= sprintf('google.visualization.events.addListener(chart, "%s", ', $event);
        //                $out .= sprintf('function(event) { %s.%s(event); });', $this->chart->chartType, $event).PHP_EOL;
        //            }
        //        }

        $out .= "}".PHP_EOL;
        $out .= $this->$jsClose.PHP_EOL;

        $this->$output = $out;

        return $this->$output;
    }

    /**
     * Returns the Javascript block to place in the page manually.
     *
     * @access public
     *
     * @return string Javascript code blocks.
     */
    public function getOutput()
    {
        return $this->$output;
    }

    /**
     * Builds the javascript object for the event callbacks.
     *
     * @param string Chart type.
     * @param array Array of events to apply to the chart.
     *
     * @return string Javascript code block.
     */
    private function buildEventCallbacks($chartType, $chartEvents)
    {
        $script = sprintf('if(typeof %s !== "object") { %s = {}; }', $chartType, $chartType).PHP_EOL.PHP_EOL;

        foreach($chartEvents as $event)
        {
             $script .= sprintf('%s.%s = function(event) {', $chartType, $event).PHP_EOL;

             $callback = $this->$callbackPath.$chartType.'.'.$event.'.js';
             $callbackScript = file_get_contents($callback);

             if($callbackScript !== false)
             {
                $script .= $callbackScript.PHP_EOL;
             } else {
                 $this->setError(__METHOD__, 'Error loading javascript file, in '.$callback.'.js');
             }

             $script .= "};".PHP_EOL;
        }

        $out  = $this->$jsOpen.PHP_EOL;
        $out .= $script;
        $out .= $this->$jsClose.PHP_EOL;

        return $out;
    }
}
