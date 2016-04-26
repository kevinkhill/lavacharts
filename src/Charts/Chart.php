<?php

namespace Khill\Lavacharts\Charts;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Options;
use \Khill\Lavacharts\JsonConfig;
use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Configs\Legend;
use \Khill\Lavacharts\Configs\Tooltip;
use \Khill\Lavacharts\Configs\TextStyle;
use \Khill\Lavacharts\Configs\ChartArea;
use \Khill\Lavacharts\Configs\Animation;
use \Khill\Lavacharts\Configs\EventManager;
use \Khill\Lavacharts\Configs\BackgroundColor;
use \Khill\Lavacharts\DataTables\DataTable;
use \Khill\Lavacharts\Javascript\JavascriptFactory;
use \Khill\Lavacharts\Exceptions\DataTableNotFound;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * Chart Class, Parent to all charts.
 *
 * Has common properties and methods used between all the different charts.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Charts
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class Chart extends JsonConfig
{
    /**
     * The chart's unique label.
     *
     * @var \Khill\Lavacharts\Values\Label
     */
    protected $label;

    /**
     * Sets a configuration option
     *
     * Takes an array with option => value, or an object created by
     * one of the configOptions child objects.
     *
     * @var \Khill\Lavacharts\DataTables\DataTable
     */
    protected $datatable;

    /**
     * Default configuration options for the chart.
     *
     * @var array
     */
    protected $chartDefaults = [
        'animation',
        'backgroundColor',
        'chartArea',
        'colors',
        'datatable',
        'events',
        'fontSize',
        'fontName',
        'height',
        'legend',
        'title',
        'titlePosition',
        'titleTextStyle',
        'tooltip',
        'width'
    ];


    /**
     * Builds a new chart with the given label.
     *
     * @param  \Khill\Lavacharts\Values\Label         $chartLabel Identifying label for the chart.
     * @param  \Khill\Lavacharts\DataTables\DataTable $datatable DataTable used for the chart.
     * @param  \Khill\Lavacharts\Options              $options Options fot the chart.
     * @param  array                                  $config Array of options to set on the chart.
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function __construct(Label $chartLabel, DataTable $datatable, Options $options, $config = [])
    {
        $this->label     = $chartLabel;
        $this->datatable = $datatable;

        $options->extend($this->chartDefaults);

        parent::__construct($options, $config);
    }

    /**
     * Returns the chart type.
     *
     * @since 3.0.0
     * @return string
     */
    public function getType()
    {
        return static::TYPE;
    }

    /**
     * Checks if any events have been assigned to the chart.
     *
     * @access public
     * @return bool
     */
    public function hasEvents()
    {
        return $this->options->hasValue('events');
    }

    /**
     * Retrieves the events if any have been assigned to the chart.
     *
     * @access public
     * @return \Khill\Lavacharts\Configs\EventManager
     */
    public function getEvents()
    {
        return $this->options->get('events');
    }

    /**
     * Returns the chart label.
     *
     * @access public
     * @since  3.0.0
     * @return \Khill\Lavacharts\Values\Label
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Register javascript callbacks for specific events.
     *
     * Set with an associative array where the keys are events and the values are the
     * javascript callback functions.
     *
     * Valid events are:
     * [ animationfinish | error | onmouseover | onmouseout | ready | select | statechange ]
     *
     * @access public
     * @param  array $events Array of events associated to a callback
     * @return \Khill\Lavacharts\Charts\Chart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function events($events)
    {
        if (is_array($events) === false) {
            throw new InvalidConfigValue(
                static::TYPE . '->' . __FUNCTION__,
                'array',
                'who\'s keys are one of '.Utils::arrayToPipedString($this->defaultEvents)
            );
        }

        foreach ($events as $event => $callback) {
            if (Utils::nonEmptyString($callback) === false) {
                throw new InvalidConfigValue(
                    static::TYPE . '->' . __FUNCTION__,
                    'string'
                );
            }

            $this->events->set($event, $callback);
        }

        return $this;
    }

    /**
     * Assigns a datatable to use for the Chart.
     *
     * @deprecated Apply the DataTable to the chart in the constructor.
     * @access public
     * @param  \Khill\Lavacharts\DataTables\DataTable $datatable
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function datatable(DataTable $datatable)
    {
        $this->datatable = $datatable;

        return $this;
    }

    /**
     * Sets any configuration option, with no checks for type / validity
     *
     *
     * This is method was added in 2.5 as a bandaid to remove the handcuffs from
     * users who want to add options that Google has added, that I have not.
     * I didn't intend to restrict the user to only select options, as the
     * goal was to type check and validate. This method can be used to set
     * any option, just pass in arrays with key value pairs for any setting.
     *
     * If the setting is an object, per the google docs, then use multi-dimensional
     * arrays and they will be converted upon rendering.
     *
     * @access public
     * @since  3.0.0
     * @param  array $optionArray Array of customization options for the chart
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function customize($optionArray)
    {
        return $this->options->setOptions($optionArray, false);
    }

    /**
     * Returns the DataTable
     *
     * @access public
     * @since  3.0.0
     * @return \Khill\Lavacharts\DataTables\DataTable
     * @throws \Khill\Lavacharts\Exceptions\DataTableNotFound
     */
    public function getDataTable()
    {
        if (is_null($this->datatable)) {
            throw new DataTableNotFound($this);
        }

        return $this->datatable;
    }

    /**
     * Returns a JSON string representation of the datatable.
     *
     * @access public
     * @since  2.5.0
     * @throws \Khill\Lavacharts\Exceptions\DataTableNotFound
     * @return string
     */
    public function getDataTableJson()
    {
        return json_encode($this->getDataTable());
    }

    /**
     * Outputs the chart javascript into the page.
     *
     * Pass in a string of the html elementID that you want the chart to be
     * rendered into.
     *
     * @deprecated Use the Lavacharts master object to keep track of charts to render.
     * @codeCoverageIgnore
     * @access public
     * @since  2.0.0
     * @param  string $elemId The id of an HTML element to render the chart into.
     * @return string Javascript code blocks
     * @throws \Khill\Lavacharts\Exceptions\InvalidElementId
     *
     */
    public function render($elemId)
    {
        trigger_error("Rendering directly from charts is deprecated. Use the render method off your main Lavacharts object.", E_USER_DEPRECATED);
        $jsf = new JavascriptFactory;

        return $jsf->getChartJs($this, $elemId);
    }

    /*****************************************************************************************************************
     ** Options
     *****************************************************************************************************************/

    /**
     * Set the animation options for a chart.
     *
     * @access public
     * @param  array $animationConfig Animation options
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function animation($animationConfig)
    {
        return $this->setOption(__FUNCTION__, new Animation($animationConfig));
    }

    /**
     * The background color for the main area of the chart.
     *
     * Can be a simple HTML color string, or hex code, for example: 'red' or '#00cc00'
     *
     * @access public
     * @param  array $backgroundColorConfig Options for the chart's background color
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function backgroundColor($backgroundColorConfig)
    {
        return $this->setOption(__FUNCTION__, new BackgroundColor($backgroundColorConfig));
    }

    /**
     * An object with members to configure the placement and size of the chart area
     * (where the chart itself is drawn, excluding axis and legends).
     *
     * Two formats are supported: a number, or a number followed by %.
     * A simple number is a value in pixels; a number followed by % is a percentage.
     *
     *
     * @access public
     * @param  array $chartAreaConfig Options for the chart area.
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function chartArea($chartAreaConfig)
    {
        return $this->setOption(__FUNCTION__, new ChartArea($chartAreaConfig));
    }

    /**
     * The colors to use for the chart elements.
     *
     * An array of strings, where each element is an HTML color string
     * for example:['red','#004411']
     *
     *
     * @access public
     * @param  array $colorArray
     * @return \Khill\Lavacharts\Charts\Chart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function colors($colorArray)
    {
        if (Utils::arrayValuesCheck($colorArray, 'string') === false) {
            throw new InvalidConfigValue(
                static::TYPE . '->' . __FUNCTION__,
                'array',
                'with valid HTML colors'
            );
        }

        return $this->setOption(__FUNCTION__, $colorArray);
    }

    /**
     * The default font size, in pixels, of all text in the chart.
     *
     * You can override this using properties for specific chart elements.
     *
     *
     * @access public
     * @param  integer $fontSize
     * @return \Khill\Lavacharts\Charts\Chart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function fontSize($fontSize)
    {
        return $this->setIntOption(__FUNCTION__, $fontSize);
    }

    /**
     * The default font face for all text in the chart.
     *
     * You can override this using properties for specific chart elements.
     *
     *
     * @access public
     * @param  string $fontName
     * @return \Khill\Lavacharts\Charts\Chart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function fontName($fontName)
    {
        return $this->setStringOption(__FUNCTION__, $fontName);
    }

    /**
     * Height of the chart, in pixels.
     *
     *
     * @access public
     * @param  int $height
     * @return \Khill\Lavacharts\Charts\Chart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function height($height)
    {
        return $this->setIntOption(__FUNCTION__, $height);
    }

    /**
     * An object with members to configure various aspects of the legend.
     *
     * To specify properties of this object, pass in an array of valid options.
     *
     *
     * @access public
     * @param  array $legendConfig Options for the chart's legend.
     * @return \Khill\Lavacharts\Charts\Chart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function legend($legendConfig)
    {
        return $this->setOption(__FUNCTION__, new Legend($legendConfig));
    }

    /**
     * Text to display above the chart.
     *
     *
     * @access public
     * @param  string $title
     * @return \Khill\Lavacharts\Charts\Chart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function title($title)
    {
        return $this->setStringOption(__FUNCTION__, $title);
    }

    /**
     * Where to place the chart title, compared to the chart area.
     *
     * Supported values:
     * 'in'   - Draw the title inside the chart area.
     * 'out'  - Draw the title outside the chart area.
     * 'none' - Omit the title.
     *
     *
     * @access public
     * @param  string $titlePosition
     * @return \Khill\Lavacharts\Charts\Chart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function titlePosition($titlePosition)
    {
        $values = [
            'in',
            'out',
            'none'
        ];

        return $this->setStringInArrayOption(__FUNCTION__, $titlePosition, $values);
    }

    /**
     * An array of options for defining the title text style.
     *
     * @access public
     * @uses   TextStyle
     * @param  TextStyle $textStyle
     * @return \Khill\Lavacharts\Charts\Chart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function titleTextStyle($textStyle)
    {
        return $this->setOption(__FUNCTION__, new TextStyle($textStyle));
    }

    /**
     * An object with members to configure various tooltip elements.
     *
     *
     * @param  array $tooltip Options for the tooltips
     * @return \Khill\Lavacharts\Charts\Chart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function tooltip($tooltip)
    {
        return $this->setOption(__FUNCTION__, new Tooltip($tooltip));
    }

    /**
     * Width of the chart, in pixels.
     *
     * @param  int $width
     * @return \Khill\Lavacharts\Charts\Chart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function width($width)
    {
        return $this->setIntOption(__FUNCTION__, $width);
    }
}
