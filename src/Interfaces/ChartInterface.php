<?php namespace Khill\Lavacharts\Interfaces;

/**
 * Chart Interface
 *
 * Defines the methods needed for all charts.
 *
 *
 * @package    Lavacharts
 * @subpackage Interfaces
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Javascript\JavascriptFactory;
use Khill\Lavacharts\Configs\DataTable;
use Khill\Lavacharts\Configs\Legend;
use Khill\Lavacharts\Configs\Tooltip;
use Khill\Lavacharts\Configs\TextStyle;
use Khill\Lavacharts\Configs\ChartArea;
use Khill\Lavacharts\Configs\BackgroundColor;


interface ChartInterface {

    /**
     * Builds a new chart with a label.
     *
     * @param string $chartLabel Label for the chart accessed via the Volcano.
     */
    public function __construct($chartLabel);

    /**
     * Sets a configuration option
     *
     * Takes an array with option => value, or an object created by
     * one of the configOptions child objects.
     *
     * @param mixed $o
     *
     * @return this
     */
    public function addOption($o);

    /**
     * Sets configuration options from array of values
     *
     * You can set the options all at once instead of passing them individually
     * or chaining the functions from the chart objects.
     *
     * @param  array                 $o
     * @throws InvalidConfigProperty
     * @throws InvalidConfigValue
     * @return Chart
     */
    public function setOptions($o);

    /**
     * Gets the current chart options.
     *
     * @return array
     */
    public function getOptions();

    /**
     * Gets a specific option from the array.
     *
     * @param  string             $o Which option to fetch
     * @throws InvalidConfigValue
     * @return mixed
     */
    public function getOption($o);

    /**
     * Checks if any events have been assigned to the chart.
     *
     * @return bool
     */
    public function hasEvents();

    /**
     * Checks if any events have been assigned to the chart.
     *
     * @return bool
     */
    public function getEvents();

    /**
     * Returns a JSON string representation of the datatable.
     *
     * @since  v2.5.0
     * @return string
     */
    public function getDataTableJson();

    /**
     * The background color for the main area of the chart. Can be either a simple
     * HTML color string, for example: 'red' or '#00cc00', or a backgroundColor object
     *
     * @uses  BackgroundColor
     * @param BackgroundColor $bc
     *
     * @return Chart
     */
    public function backgroundColor(BackgroundColor $bc);

    /**
     * An object with members to configure the placement and size of the chart area
     * (where the chart itself is drawn, excluding axis and legends).
     * Two formats are supported: a number, or a number followed by %.
     * A simple number is a value in pixels; a number followed by % is a percentage.
     *
     * @uses  ChartArea
     * @param ChartArea $ca
     *
     * @return Chart
     */
    public function chartArea(ChartArea $ca);

    /**
     * The colors to use for the chart elements. An array of strings, where each
     * element is an HTML color string, for example: colors:['red','#004411'].
     *
     * @param  array              $cArr
     * @throws InvalidConfigValue
     *
     * @return Chart
     */
    public function colors($cArr);

    /**
     * Assigns wich Datatable will be used for this Chart.
     *
     * If a label is provided then the defined Datatable will be used.
     * If called with no argument, or the chart is attempted to be generated
     * without calling this function, the chart will search for a Datatable with
     * the same label as the Chart.
     *
     * @uses  Datatable
     * @param Datatable
     *
     * @return Chart
     */
    public function datatable(Datatable $d);

    /**
     * Register javascript callbacks for specific events.
     *
     * Valid values include:
     * [ animationfinish | error | onmouseover | onmouseout | ready | select ]
     * associated to a respective pre-defined javascript function as the callback.
     *
     * @param  array              $e Array of events associated to a callback
     * @throws InvalidConfigValue
     *
     * @return Chart
     */
    public function events($e);

    /**
     * The default font size, in pixels, of all text in the chart. You can
     * override this using properties for specific chart elements.
     *
     * @param  int                $fs
     * @throws InvalidConfigValue
     *
     * @return Chart
     */
    public function fontSize($fs);

    /**
     * The default font face for all text in the chart. You can override this
     * using properties for specific chart elements.
     *
     * @param  string             $fn
     * @throws InvalidConfigValue
     *
     * @return Chart
     */
    public function fontName($fn);

    /**
     * Height of the chart, in pixels.
     *
     * @param  int                $h
     * @throws InvalidConfigValue
     *
     * @return Chart
     */
    public function height($h);

    /**
     * An object with members to configure various aspects of the legend. To
     * specify properties of this object, create a new legend() object, set the
     * values then pass it to this function or to the constructor.
     *
     * @uses   Legend
     * @param  Legend             $l
     * @throws InvalidConfigValue
     *
     * @return Chart
     */
    public function legend(Legend $l);

    /**
     * Returns a JSON string representation of the chart's properties.
     *
     * @return string
     */
    public function optionsToJson();

    /**
     * Outputs the chart javascript into the page
     *
     * Pass in a string of the html elementID that you want the chart to be
     * rendered into.
     *
     * @since  v2.0.0
     * @param  string           $ei The id of an HTML element to render the chart into.
     * @throws InvalidElementId
     *
     * @return string Javscript code blocks
     */
    public function render($ei);

    /**
     * Text to display above the chart.
     *
     * @param  string             $t
     * @throws InvalidConfigValue
     *
     * @return Chart
     */
    public function title($t);

    /**
     * Where to place the chart title, compared to the chart area. Supported values:
     * 'in' - Draw the title inside the chart area.
     * 'out' - Draw the title outside the chart area.
     * 'none' - Omit the title.
     *
     * @param  string             $tp
     * @throws InvalidConfigValue
     *
     * @return Chart
     */
    public function titlePosition($tp);

    /**
     * An object that specifies the title text style. create a new textStyle()
     * object, set the values then pass it to this function or to the constructor.
     *
     * @uses   TextStyle
     * @param  TextStyle          $ts
     * @throws InvalidConfigValue
     *
     * @return Chart
     */
    public function titleTextStyle(TextStyle $ts);

    /**
     * An object with members to configure various tooltip elements. To specify
     * properties of this object, create a new tooltip() object, set the values
     * then pass it to this function or to the constructor.
     *
     * @uses   Tooltip
     * @param  Tooltip            $t
     * @throws InvalidConfigValue
     *
     * @return Chart
     */
    public function tooltip(Tooltip $t);

    /**
     * Width of the chart, in pixels.
     *
     * @param  int                $w
     * @throws InvalidConfigValue
     *
     * @return Chart
     */
    public function width($w);

    /**
     * function for easy creation of exceptions
     */
    public function invalidConfigValue($func, $type, $extra = '');
}
