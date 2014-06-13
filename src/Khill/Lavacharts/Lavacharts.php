<?php namespace Khill\Lavacharts;

/**
 * Lavacharts - A PHP wrapper library for the Google Chart API
 *
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

class Lavacharts
{
    /**
     * @var array Lavachart configuration options.
     */
    protected static $config = array();

    /**
     * @var string Holds the HTML and javscript to be output into the browser.
     */
    protected static $output = null;

    /**
     * @var string ID of the HTML element that will be receiving the chart.
     */
    protected static $elementID = null;

    /**
     * @var string Version of Google's DataTable.
     */
    protected static $dataTableVersion = '0.6';

    /**
     * @var string Opening javascript tag.
     */
    protected static $jsOpen = '<script type="text/javascript">';

    /**
     * @var string Closing javascript tag.
     */
    protected static $jsClose = '</script>';

    /**
     * @var string Javscript block with a link to Google's Chart API.
     */
    protected static $googleAPI = '<script type="text/javascript" src="//google.com/jsapi"></script>';

    /**
     * @var string Lavacharts root namespace
     */
    protected static $rootSpace = 'Khill\\Lavacharts\\';

    /**
     * @var boolean Status of chart generation errors.
     */
    protected static $hasError = false;

    /**
     * @var array Array containing the list of errors.
     */
    protected static $errorLog = array();

    /**
     * @var array Holds all of the defined Charts.
     */
    protected static $chartsAndTables = array();

    /**
     * @var array Types of charts that can be created.
     */
    protected static $chartClasses = array(
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
    protected static $configClasses = array(
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
    protected static $validGlobals = array(
        'errorWrap',
        'errorClass',
        'textStyle'
    );

    /*
    public function __construct(Repository $config)
    {
        self::setGlobals($config);
    }*/

    /**
     * Magic function to reduce repetitive coding and create aliases.
     *
     * @access public
     *
     * @param string $member Name of method
     * @param array $arguments Passed arguments
     *
     * @throws Khill\Lavacharts\Exceptions\InvalidLavaObject
     * @return mixed Returns Charts, DataTables, and Config Objects
     */
    public function __call($member, $arguments)
    {
        if (in_array($member, self::$configClasses)) {
            return self::configObjectFactory($member, empty($arguments[0]) ? array() : $arguments);
        } else {
            if (in_array($member, self::$chartClasses)) {
                return self::chartAndTableFactory($member, empty($arguments[0]) ? '' : $arguments[0]);
            } else {
                throw new InvalidLavaObject($member);
            }
        }
    }

    /**
     * Builds the Javascript code block
     *
     * This will build the script block for the actual chart and passes it
     * back to output function of the calling chart object. If there are any
     * events defined, they will be automatically be attached to the chart and
     * pulled from the callbacks folder.
     *
     * @access public
     *
     * @param string $chart Passed from the calling chart.
     *
     * @return string Javascript code block.
     */
    public static function buildScriptBlock($chart)
    {
        self::$elementID = $chart->elementID;

        $out = self::$googleAPI.PHP_EOL;

        //        if(is_array($chart->events) && count($chart->events) > 0)
        //        {
        //            $out .= self::_build_event_callbacks($chart->chartType, $chart->events);
        //        }

        $out .= self::$jsOpen.PHP_EOL;

        if ($chart->elementID == null) {
            $out .= 'alert("Error calling '.$chart->chartType.'(\''.$chart->chartLabel.'\')->outputInto(), requires a valid html elementID.");'.PHP_EOL;
        }

        if (
            isset($chart->data) === false &&
            isset(self::$chartsAndTables['DataTable'][$chart->dataTable]) === false
        ) {
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

        if (isset($chart->data) && $chart->dataTable == 'local') {
            $out .= sprintf(
                'var data = new google.visualization.DataTable(%s, %s);',
                $chart->data->toJSON(),
                self::$dataTableVersion
            ).PHP_EOL;
        }

        if (isset(self::$chartsAndTables['DataTable'][$chart->dataTable])) {
            $out .= sprintf(
                'var data = new google.visualization.DataTable(%s, %s);',
                self::$chartsAndTables['DataTable'][$chart->dataTable]->toJSON(),
                self::$dataTableVersion
            ).PHP_EOL;
        }

        $out .= sprintf('var options = %s;', $chart->optionsToJSON()).PHP_EOL;
        $out .= sprintf('var chart = new google.visualization.%s', $chart->chartType);
        $out .= sprintf("(document.getElementById('%s'));", $chart->elementID).PHP_EOL;
        $out .= 'chart.draw(data, options);'.PHP_EOL;

        //        if(is_array($chart->events) && count($chart->events) > 0)
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
     * Sets global configuration options for the whole Lavachart library.
     *
     * Accepted config options include:
     * errorPrepend: An html string
     *
     * @access public
     *
     * @param array $config Array of configurations options
     *
     * @return Khill\Lavachart
     */
    public static function setGlobals($config)
    {
        if (is_array($config)) {
            foreach ($config as $option => $value) {
                if (in_array($option, self::$validGlobals)) {
                    self::$config[$option] = $value;
                } else {
                    throw new InvalidConfigProperty(
                        __METHOD__,
                        $option,
                        self::$validGlobals
                    );
                }
            }
        } else {
            throw new InvalidConfigValue(
                get_class(),
                __METHOD__,
                'array'
            );
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
     * @access public
     *
     * @param int $width Width of the containing div (optional).
     * @param int $height Height of the containing div (optional).
     *
     * @return string HTML div element.
     */
    public static function div($width = 0, $height = 0)
    {
        if ($width == 0 || $height == 0) {
            if (isset(self::$elementID)) {
                return sprintf('<div id="%s"></div>', self::$elementID);
            } else {
                $this->setError(__METHOD__, 'Error, output element ID is not set.');
            }
        } else {
            if ((is_int($width) && $width > 0) && (is_int($height) && $height > 0)) {
                if (isset(self::$elementID)) {
                    $format = '<div id="%s" style="width:%spx; height:%spx;"></div>';
                    return sprintf($format, self::$elementID, $width, $height);
                } else {
                    $this->setError(__METHOD__, 'Error, output element ID is not set.');
                }
            } else {
                $this->setError(__METHOD__, 'Invalid div width & height, must be type (int) > 0');
            }
        }
    }

    /**
     * Returns the Javascript block to place in the page manually.
     *
     * @access public
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
     * @access public
     *
     * @return boolean true if any errors we created while building charts,
     * otherwise false.
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
     * @access public
     * @return null|string null if there are no errors, otherwise a string with the
     * errors
     */
    public static function getErrors()
    {
        if (self::$hasError === true && count(self::$errorLog) > 0) {
            $errors = '';

            foreach (self::$errorLog as $where => $error) {
                if (isset(self::$config['errorWrap'])) {
                    $errors .= '<' . self::$config['errorWrap'];

                    if (isset(self::$config['errorClass'])) {
                        $errors .= ' class="' . self::$config['errorClass'] . '">';
                    }
                }

                $errors .= '['.$where.'] -> '.$error;

                if (isset(self::$config['errorWrap'])) {
                    $errors .= '</' . self::$config['errorWrap'] . '>';
                }
            }

            return $errors;
        } else {
            return null;
        }
    }

    /**
     * Sets an error message.
     *
     * @access private
     *
     * @param string $where Where the error occured.
     * @param string $what What the error was.
     */
    private static function setError($where, $what)
    {
        self::$hasError = true;
        self::$errorLog[$where] = $what;
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
     *
     * @param  string $objType Which type of object to generate.
     * @param  string $objLabel ype Label applied to generated object.
     *
     * @return Khill\Lavachart\Chart|Khill\Lavachart\DataTable
     */
    private static function chartAndTableFactory($objType, $objLabel)
    {
        if (is_string($objLabel) && $objLabel != '') {
            if (! isset(self::$chartsAndTables[$objType][$objLabel])) {
                if ($objType == 'DataTable') {
                    $class = self::$rootSpace.'Configs\\'.$objType;
                } else {
                    $class = self::$rootSpace.'Charts\\'.$objType;
                }

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
     *
     * @param  string $configObject
     * @param  array $options
     *
     * @return Khill\Lavacharts\Configs\ConfigOptions configuration object
     */
    private static function configObjectFactory($configObject, $options)
    {
        if ($configObject == 'JsDate') {
            $jsDate = new JsDate();

            return $jsDate->parse($options);
        } else {
            $class = self::$rootSpace.'Configs\\'.$configObject;

            return empty($options[0]) ? new $class() : new $class($options[0]);
        }
    }

    /**
     * Builds the javascript object for the event callbacks.
     *
     * @param string Chart type.
     * @param array Array of events to apply to the chart.
     *
     * @return string Javascript code block.
     */
    /*
    private static function _build_event_callbacks($chartType, $chartEvents)
    {
        $script = sprintf('if(typeof %s !== "object") { %s = {}; }', $chartType, $chartType).PHP_EOL.PHP_EOL;

        foreach($chartEvents as $event)
        {
             $script .= sprintf('%s.%s = function(event) {', $chartType, $event).PHP_EOL;

             $callback = self::$callbackPath.$chartType.'.'.$event.'.js';
             $callbackScript = file_get_contents($callback);

             if($callbackScript !== false)
             {
                $script .= $callbackScript.PHP_EOL;
             } else {
                 self::setError(__METHOD__, 'Error loading javascript file, in '.$callback.'.js');
             }

             $script .= "};".PHP_EOL;
        }

        $tmp = self::$jsOpen.PHP_EOL;
        $tmp .= $script;
        $tmp .= self::$jsClose.PHP_EOL;

        return $tmp;
    }
    */
}
