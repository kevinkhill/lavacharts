<?php namespace Khill\Lavacharts;

use Illuminate\View\Environment;
use Illuminate\Config\Repository;
use Khill\Lavacharts\Configs\jsDate;
use Khill\Lavacharts\Configs\textStyle;

class Lavacharts
{
    /**
     * Illuminate view environment.
     *
     * @var Illuminate\View\Environment
     */
    protected $view;

    /**
     * Illuminate config repository.
     *
     * @var Illuminate\Config\Repository
     */
    protected $config;

    /**
     * Holds all the html and javscript to be output into the browser.
     *
     * @var string
     */
    protected static $output = NULL;

    /**
     * The ID of the HTML element that will be receiving the chart.
     *
     * @var string
     */
    protected static $elementID = NULL;

    /**
     * Version of Google's DataTable.
     *
     * @var string
     */
    protected static $dataTableVersion = '0.6';

    /**
     * Opening Javascript tag.
     *
     * @var string
     */
    protected static $jsOpen = '<script type="text/javascript">';

    /**
     * Closing Javascript tag.
     *
     * @var string
     */
    protected static $jsClose = '</script>';

    /**
     * Javscript block with a link to Google's Chart API.
     *
     * @var string
     */
    protected static $googleAPI = '<script type="text/javascript" src="https://www.google.com/jsapi"></script>';

    /**
     * Root Namespace
     *
     * @var string
     */
    protected static $rootSpace = 'Khill\\Lavacharts\\';

    /**
     * Property defining if the generation of charts occured any errors.
     *
     * @var boolean
     */
    protected static $hasError = FALSE;

    /**
     * Array containing the list of errors.
     *
     * @var array
     */
    protected static $errorLog = array();

    /**
     * Holds all of the defined Charts.
     *
     * @var array
     */
    protected static $chartsAndTables = array();

    /**
     * Currently supported types of charts that can be created.
     * Used by the magic __call function to prevent errors.
     *
     * @var array
     */
    protected static $supportedClasses = array(
        'DataTable',
        'LineChart',
        'AreaChart',
        'PieChart',
        'DonutChart',
        'ColumnChart',
        'GeoChart'
    );

    /**
     * Holds all of the defined configuration class names.
     *
     * @var array
     */
    protected static $configClasses = array(
        'configOptions',
        'Axis',
//        'DataCell',
        'backgroundColor',
        'chartArea',
        'colorAxis',
        'hAxis',
        'jsDate',
        'legend',
        'magnifyingGlass',
        'textStyle',
        'tooltip',
        'sizeAxis',
        'slice',
        'vAxis'
    );

    /**
     * Create a new Lavacharts instance.
     *
     * @param  Illuminate\View\Environment  $view
     * @param  Illuminate\Config\Repository  $config
     * @return void
     */
    public function __construct(Environment $view, Repository $config)
    {
        $this->view   = $view;
        $this->config = $config;
    }

    /**
     * Magic function to reduce repetitive coding and create aliases.
     *
     * This is never called directly.
     *
     * @param string Name of method
     * @param array Passed arguments
     * @return object Returns Charts, DataTables, and Config Objects
     */
    public function __call($member, $arguments)
    {
        if(in_array($member, self::$configClasses))
        {
            return self::_config_object_factory($member, empty($arguments[0]) ? array() : $arguments);
        } else {
            if(in_array($member, self::$supportedClasses))
            {
                return self::_chart_and_table_factory($member, empty($arguments[0]) ? '' : $arguments[0]);
            } else {
                exit(get_class($this).'::'.$member.'() Is Undefined');
            }
        }
    }

    /**
     * Creates and stores Chart/DataTable
     *
     * If there is no label, then the Chart/DataTable is just returned.
     * If there is a label, the Chart/DataTable is stored within the Lavacharts
     * objects in an array, accessable via a call to the type of object, with
     * the label as the paramater.
     *
     * @access private
     * @param string Which type of object to generate.
     * @param string Label applied to generated object.
     * @return mixed Returns Charts or DataTables
     */
    private static function _chart_and_table_factory($objType, $objLabel)
    {
        if(is_string($objLabel) && $objLabel != '')
        {
            if( ! isset(self::$chartsAndTables[$objType][$objLabel]))
            {
                $class = $objType == 'DataTable' ? self::$rootSpace.'Configs\\'.$objType : self::$rootSpace.'Charts\\'.$objType;
                self::$chartsAndTables[$objType][$objLabel] = new $class($objLabel);
            }

            return self::$chartsAndTables[$objType][$objLabel];
        } else {
            return new $objType();
        }
    }

    /**
     * Creates configuration objects to save a step instansiating and allow for
     * chaining directly from creation.
     *
     * @access private
     * @param string $configObject
     * @return object configuration object
     */
    private static function _config_object_factory($configObject, $options)
    {
        if(in_array($configObject, self::$configClasses))
        {
            if($configObject == 'jsDate')
            {
                $jsDate = new jsDate();
                return $jsDate->parseArray($options);
            } else {
                $class = self::$rootSpace.'Configs\\'.$configObject;
                return empty($options[0]) ? new $class() : new $class($options[0]);
            }
        } else {
            exit('[Lavacharts::'.$configObject.'()] is not a valid configObject');
        }
    }

    /**
     * Builds a div html element for a chart to be rendered into.
     *
     * Calling with no arguments will return a div with the ID set to what was
     * given to the outputInto() function.
     *
     * Passing two (int)s will set the width and height respectivly and the div
     * ID will be set via the string given in the outputInto() function.
     *
     *
     * This is useful for the AnnotatedTimeLine Chart since it MUST have explicitly
     * defined dimensions of the div it is rendered into.
     *
     * The other charts do not require height and width, but do require an ID of
     * the div that will be receiving the chart.
     *
     * @param int Width of the containing div (optional).
     * @param int Height of the containing div (optional).
     * @return string HTML div element.
     */
    public static function div($width = 0, $height = 0)
    {
        if($width == 0 || $height == 0)
        {
            if(isset(self::$elementID))
            {
                return sprintf('<div id="%s"></div>', self::$elementID);
            } else {
                $this->_set_error(__METHOD__, 'Error, output element ID is not set.');
            }
        } else {
            if((is_int($width) && $width > 0) && (is_int($height) && $height > 0))
            {
                if(isset(self::$elementID))
                {
                    $format = '<div id="%s" style="width:%spx;height:%spx;"></div>';
                    return sprintf($format, self::$elementID, $width, $height);
                } else {
                    $this->_set_error(__METHOD__, 'Error, output element ID is not set.');
                }
            } else {
                $this->_set_error(__METHOD__, 'Invalid div width & height, must be type (int) > 0');
            }
        }
    }

    /**
     * Returns the Javascript block to place in the page manually.
     *
     * @return string Javascript code blocks.
     */
    public static function getOutput()
    {
        return self::$output;
    }

    /**
     * Checks if any errors have occured.
     *
     * @return boolean TRUE if any errors we created while building charts,
     * otherwise FALSE.
     */
    public static function hasErrors()
    {
        return self::$hasError;
    }

    /**
     * Gets the error messages.
     *
     * Each error message is wrapped in the HTML element defined within the
     * configuration for lavacharts.
     *
     * @return mixed NULL if there are no errors, otherwise a string with the
     * errors
     */
    public static function getErrors()
    {
        if(self::$hasError === TRUE && count(self::$errorLog) > 0)
        {
            $errors = '';

            foreach(self::$errorLog as $where => $error)
            {
                $errors .= $this->config->get('lavacharts::errorPrepend');
                $errors .= '['.$where.'] -> '.$error;
                $errors .= $this->config->get('lavacharts::errorAppend');
            }

            return $errors;
        } else {
            return NULL;
        }
    }

    /**
     * Sets an error message.
     *
     * @param string Where the error occured.
     * @param string What the error was.
     */
    public static function _set_error($where, $what)
    {
        self::$hasError = TRUE;
        self::$errorLog[$where] = $what;
    }

    /**
     * Builds the Javascript code block
     *
     * This will build the script block for the actual chart and passes it
     * back to output function of the calling chart object. If there are any
     * events defined, they will be automatically be attached to the chart and
     * pulled from the callbacks folder.
     *
     * @param string Passed from the calling chart.
     * @return string Javascript code block.
     */
    public static function _build_script_block($chart)
    {
        self::$elementID = $chart->elementID;

        $out = self::$googleAPI.PHP_EOL;

//        if(isset($chart->events) && is_array($chart->events) && count($chart->events) > 0)
//        {
//            $out .= self::_build_event_callbacks($chart->chartType, $chart->events);
//        }

        $out .= self::$jsOpen.PHP_EOL;

        if($chart->elementID == NULL)
        {
            $out .= 'alert("Error calling '.$chart->chartType.'(\''.$chart->chartLabel.'\')->outputInto(), requires a valid html elementID.");'.PHP_EOL;
        }

        if(isset($chart->data) === FALSE && isset(self::$chartsAndTables['DataTable'][$chart->dataTable]) === FALSE)
        {
            $out .= 'alert("No DataTable has been defined for '.$chart->chartType.'(\''.$chart->chartLabel.'\').");'.PHP_EOL;
        }

        switch($chart->chartType)
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

        if(isset($chart->data) && $chart->dataTable == 'local')
        {
            $data = $chart->data->toJSON();
            $format = 'var data = new google.visualization.DataTable(%s, %s);';
            $out .= sprintf($format, $data, self::$dataTableVersion).PHP_EOL;
        }
        if(isset(self::$chartsAndTables['DataTable'][$chart->dataTable]))
        {
            $data = self::$chartsAndTables['DataTable'][$chart->dataTable];
            $format = 'var data = new google.visualization.DataTable(%s, %s);';
            $out .= sprintf($format, $data->toJSON(), self::$dataTableVersion).PHP_EOL;
        }

        $out .= "var options = ".$chart->optionsToJSON().";".PHP_EOL;
        $out .= "var chart = new google.visualization.".$chart->chartType;
            $out .= sprintf("(document.getElementById('%s'));", $chart->elementID).PHP_EOL;
        $out .= "chart.draw(data, options);".PHP_EOL;

//        if(isset($chart->events) && count($chart->events) > 0)
//        {
//            foreach($chart->events as $event)
//            {
//                $out .= sprintf('google.visualization.events.addListener(chart, "%s", ', $event);
//                $out .= sprintf('function(event) { %s.%s(event); });', $chart->chartType, $event).PHP_EOL;
//            }
//        }

        $out .= "}".PHP_EOL;

        $out .= self::$jsClose.PHP_EOL;

        self::$output = $out;

        return self::$output;
    }

    /**
     * Builds the javascript object for the event callbacks.
     *
     * @param string Chart type.
     * @param array Array of events to apply to the chart.
     * @return string Javascript code block.
     */
    /*
    public static function _build_event_callbacks($chartType, $chartEvents)
    {
        $script = sprintf('if(typeof %s !== "object") { %s = {}; }', $chartType, $chartType).PHP_EOL.PHP_EOL;

        foreach($chartEvents as $event)
        {
             $script .= sprintf('%s.%s = function(event) {', $chartType, $event).PHP_EOL;

             $callback = self::$callbackPath.$chartType.'.'.$event.'.js';
             $callbackScript = file_get_contents($callback);

             if($callbackScript !== FALSE)
             {
                $script .= $callbackScript.PHP_EOL;
             } else {
                 self::_set_error(__METHOD__, 'Error loading javascript file, in '.$callback.'.js');
             }

             $script .= "};".PHP_EOL;
        }

        $tmp = self::$jsOpen.PHP_EOL;
        $tmp .= $script;
        $tmp .= self::$jsClose.PHP_EOL;

        return $tmp;
    }
    */

    /**
     * Enables/Disables Global Text Styles
     *
     * @param boolean $enabled
     * @param textStyle $globalTextStyle
     */
    public static function globalTextStyle(boolean $enabled, textStyle $globalTextStyle = NULL)
    {

        $this->config->set('lavacharts::globalTextStyleEnabled', $enabled);

        if( ! is_null($globalTextStyle))
        {
            $this->config->set('lavacharts::globalTextStyle', $globalTextStyle);
        }
    }

}
