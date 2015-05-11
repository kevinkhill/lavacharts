<?php

namespace Khill\Lavacharts;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Volcano;
use \Khill\Lavacharts\JavascriptFactory;
use \Khill\Lavacharts\Configs\DataTable;
use \Khill\Lavacharts\Dashboard\Dashboard;
use \Khill\Lavacharts\Dashboard\ChartWrapper;
use \Khill\Lavacharts\Dashboard\ControlWrapper;
use \Khill\Lavacharts\Exceptions\ChartNotFound;
use \Khill\Lavacharts\Exceptions\InvalidChartLabel;
use \Khill\Lavacharts\Exceptions\InvalidLavaObject;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;
use \Khill\Lavacharts\Exceptions\InvalidEventCallback;
use \Khill\Lavacharts\Exceptions\InvalidDivDimensions;
use \Khill\Lavacharts\Exceptions\InvalidConfigProperty;
use \Khill\Lavacharts\Exceptions\InvalidChartWrapperParams;
use \Khill\Lavacharts\Exceptions\InvalidControlWrapperParams;

/**
 * Lavacharts - A PHP wrapper library for the Google Chart API
 *
 *
 * @category  Class
 * @package   Lavacharts
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2014, KHill Designs
 * @link      http://github.com/kevinkhill/Lavacharts GitHub Repository Page
 * @link      http://kevinkhill.github.io/Lavacharts  GitHub Project Page
 * @license   http://opensource.org/licenses/MIT MIT
 */
class Lavacharts
{
    /**
     * Holds all of the defined Charts and DataTables.
     *
     * @var Volcano
     */
    public $volcano;

    /**
     * Javascript factory class for lava.js and chart js
     *
     * @var JavascriptFactory
     */
    public $jsFactory;

    /**
     * Types of charts that can be created.
     *
     * @var array
     */
    private $chartClasses = [
        'AreaChart',
        'BarChart',
        'CalendarChart',
        'ColumnChart',
        'ComboChart',
        'PieChart',
        'DonutChart',
        'GaugeChart',
        'GeoChart',
        'LineChart',
        'ScatterChart'
    ];

    /**
     * Holds all of the defined configuration class names.
     *
     * @var array
     */
    private $configClasses = [
        'Animation',
        'Annotation',
        'BackgroundColor',
        'BoxStyle',
        'ChartArea',
        'Color',
        'ColorAxis',
        'Crosshair',
        'Gradient',
        'HorizontalAxis',
        'Legend',
        'MagnifyingGlass',
        'Series',
        'SizeAxis',
        'Slice',
        'Stroke',
        'TextStyle',
        'Tooltip',
        'VerticalAxis'
    ];

    /**
     * Types of column formatters.
     *
     * @var array
     */
    private $formatClasses = [
        'DateFormat',
        'NumberFormat'
    ];

    /**
     * Types of events.
     *
     * @var array
     */
    private $eventClasses = [
        'AnimationFinish',
        'Error',
        'MouseOut',
        'MouseOver',
        'Ready',
        'Select'
    ];

    /**
     * Types of filters.
     *
     * @var array
     */
    private $filterClasses = [
        'Category',
        'ChartRange',
        'DateRange',
        'NumberRange',
        'String'
    ];

    /**
     * Creates Volcano & Javascript Factory
     *
     * @return Lavacharts
     */
    public function __construct()
    {
        if (!$this->usingComposer()) {
            require_once(__DIR__.'/Psr4Autoloader.php');

            $loader = new Psr4Autoloader;
            $loader->register();
            $loader->addNamespace('Khill\Lavacharts', __DIR__);
        }

        $this->volcano   = new Volcano;
        $this->jsFactory = new JavascriptFactory;
    }

    /**
     * Magic function to reduce repetitive coding and create aliases.
     *
     * @access public
     * @since  1.0.0
     * @param  string            $member    Name of method
     * @param  array             $arguments Passed arguments
     * @throws InvalidLavaObject
     * @throws InvalidChartLabel
     * @return mixed Returns Charts, DataTables, and Config Objects
     */
    public function __call($member, $arguments)
    {
        //Core Objects
        if ($member == 'DataTable') {
            if (isset($arguments[0])) {
                return new DataTable($arguments[0]);
            } else {
                return new DataTable;
            }
        }

        if ($member == 'Dashboard') {
            if (isset($arguments[0])) {
                return new Dashboard($arguments[0]);
            } else {
                return new Dashboard;
            }
        }

        if ($member == 'ControlWrapper') {
            if (isset($arguments[0]) === false || isset($arguments[1]) === false) {
                throw new InvalidControlWrapperParams;
            }

            return new ControlWrapper($arguments[0], $arguments[1]);
        }

        if ($member == 'ChartWrapper') {
            if (isset($arguments[0]) === false || isset($arguments[1]) === false) {
                throw new InvalidChartWrapperParams;
            }

            return new ChartWrapper($arguments[0], $arguments[1]);
        }

        //Rendering Aliases
        if ($this->strStartsWith($member, 'render')) {
            $chartType = str_replace('render', '', $member);

            if (in_array($chartType, $this->chartClasses, true) === false) {
                throw new InvalidLavaObject($chartType);
            }

            return $this->render($chartType, $arguments[0], $arguments[1]);
        }

        //Charts
        if (in_array($member, $this->chartClasses)) {
            if (isset($arguments[0]) === false) {
                throw new InvalidChartLabel;
            }

            if (Utils::nonEmptyString($arguments[0]) === false) {
                throw new InvalidChartLabel($arguments[0]);
            }

            return $this->chartFactory($member, $arguments[0]);
        }

        //ConfigObjects
        if (in_array($member, $this->configClasses)) {
            if (isset($arguments[0]) && is_array($arguments[0])) {
                return $this->configFactory($member, $arguments[0]);
            } else {
                return $this->configFactory($member);
            }
        }

        //Formatters
        if (in_array($member, $this->formatClasses)) {
            if (isset($arguments[0]) && is_array($arguments[0])) {
                return $this->formatFactory($member, $arguments[0]);
            } else {
                return $this->formatFactory($member);
            }
        }

        //Events
        if (in_array($member, $this->eventClasses)) {
            if (isset($arguments[0]) === false) {
                throw new InvalidEventCallback;
            }

            if (Utils::nonEmptyString($arguments[0]) === false) {
                throw new InvalidEventCallback($arguments[0]);

            }

            return $this->eventFactory($member, $arguments[0]);
        }

        //Missing
        if (method_exists($this, $member) === false) {
            throw new InvalidLavaObject($member);
        }
    }

    /**
     * Renders the chart into the page
     *
     * Given a chart label and an HTML element id, this will output
     * all of the necessary javascript to generate the chart.
     *
     * @access public
     * @since  2.0.0
     * @param string $chartType     Type of chart to render.
     * @param string $chartLabel    Label of a saved chart.
     * @param string $elementId     HTML element id to render the chart into.
     * @param mixed  $divDimensions Set true for div creation, or pass an array with height & width
     * @return string
     */
    public function render($chartType, $chartLabel, $elementId, $divDimensions = false)
    {
        $jsOutput = '';

        $chart = $this->volcano->getChart($chartType, $chartLabel);

        if ($this->jsFactory->coreJsRendered() === false) {
            $jsOutput = $this->jsFactory->getCoreJs();
            $this->jsFactory->coreJsRendered(true);
        }

        if ($divDimensions !== false) {
            $jsOutput .= $this->div($elementId, $divDimensions);
        }

        $jsOutput .= $this->jsFactory->getChartJs($chart, $elementId);

        return $jsOutput;
    }

    /**
     * Outputs the link to the Google JSAPI
     *
     * @access public
     * @since  2.3.0
     * @return string
     */
    public function jsapi()
    {
        $this->jsFactory->coreJsRendered(true);

        return $this->jsFactory->getCoreJs();
    }

    /**
     * Checks to see if the given chart type and title exists in the volcano storage.
     *
     * @access public
     * @since  2.4.2
     * @return bool
     */
    public function exists($type, $label)
    {
        return $this->volcano->checkChart($type, $label);
    }

    /**
     * Builds a div html element for the chart to be rendered into.
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
     * @access private
     * @since  1.0.0
     * @param  string               $elementId  Element id to apply to the div.
     * @param  array                $dimensions Height & width of the div.
     * @throws InvalidDivDimensions
     * @return string               HTML div element.
     */
    private function div($elementId, $dimensions = true)
    {
        if ($dimensions === true) {
            return sprintf('<div id="%s"></div>', $elementId);
        } else {
            if (is_array($dimensions) && ! empty($dimensions)) {
                if (array_key_exists('height', $dimensions) && array_key_exists('width', $dimensions)) {
                    $widthCheck  = (is_int($dimensions['width'])  && $dimensions['width']  > 0);
                    $heightCheck = (is_int($dimensions['height']) && $dimensions['height'] > 0);

                    if ($widthCheck && $heightCheck) {
                            return sprintf(
                                '<div id="%s" style="width:%spx; height:%spx;"></div>',
                                $elementId,
                                $dimensions['width'],
                                $dimensions['height']
                            );
                    } else {
                        throw new InvalidConfigValue(
                            __METHOD__,
                            'int',
                            'greater than 0'
                        );
                    }
                } else {
                    throw new InvalidDivDimensions();
                }
            } else {
                throw new InvalidDivDimensions();
            }
        }
    }

    /**
     * Creates and stores Charts
     *
     * If the Chart is found in the Volcano, then it is returned.
     * Otherwise, a new chart is created and stored in the Volcano.
     *
     * @access private
     * @since  2.0.0
     *
     * @uses  Chart
     * @param string $type  Type of chart to fetch or create.
     * @param string $label Label of the chart.
     * @return Chart
     */
    private function chartFactory($type, $label)
    {
        $chartObject = __NAMESPACE__ . '\\Charts\\' . $type;

        if (! $this->volcano->checkChart($type, $label)) {
            $chart = new $chartObject($label);

            $this->volcano->storeChart($chart);
        }

        return $this->volcano->getChart($type, $label);
    }

    /**
     * Creates Config Objects
     *
     * @access private
     * @since  2.0.0
     * @param string $type    Type of configObject to create.
     * @param array  $options Array of options to pass to the config object.
     * @return ConfigObject
     */
    private function configFactory($type, $options = [])
    {
        $configObj = __NAMESPACE__ . '\\Configs\\' . $type;

        return ! empty($options) ? new $configObj($options) : new $configObj;
    }

    /**
     * Creates Format Objects
     *
     * @access private
     * @since  2.0.0
     * @param string $type    Type of formatter to create.
     * @param array  $options Array of options to pass to the formatter object.
     * @return Formatter
     */
    private function formatFactory($type, $options = [])
    {
        $formatter = __NAMESPACE__ . '\\Formats\\' . $type;

        return ! empty($options) ? new $formatter($options) : new $formatter;
    }

    /**
     * Creates Event Objects
     *
     * @access private
     * @since  2.0.0
     * @param string $type Type of event to create.
     * @return Event
     */
    private function eventFactory($type, $callback)
    {
        $event = __NAMESPACE__ . '\\Events\\' . $type;

        return new $event($callback);
    }

    /**
     * Simple string starts with function
     *
     * @access private
     * @since  2.0.0
     * @param string $haystack String to search through.
     * @param array  $needle   String to search with.
     */
    private function strStartsWith($haystack, $needle)
    {
        return $needle === "" || strpos($haystack, $needle) === 0;
    }

    /**
     * Checks if running in comopser environment
     *
     * This will check if the folder 'composer' is within the path to Lavacharts.
     *
     * @access private
     * @since  2.4.0
     * @return bool
     */
    private function usingComposer()
    {
        if (strpos(realpath(__FILE__), 'composer') !== false) {
            return true;
        } else {
            return false;
        }
    }
}
