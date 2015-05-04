<?php namespace Khill\Lavacharts\Charts;

/**
 * Chart Class, Parent to all charts.
 *
 * Has common properties and methods used between all the different charts.
 *
 *
 * @package    Lavacharts
 * @subpackage Charts
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Utils;
use Khill\Lavacharts\Javascript\JavascriptFactory;
use Khill\Lavacharts\Configs\Animation;
use Khill\Lavacharts\Configs\DataTable;
use Khill\Lavacharts\Configs\Legend;
use Khill\Lavacharts\Configs\Tooltip;
use Khill\Lavacharts\Configs\TextStyle;
use Khill\Lavacharts\Configs\ChartArea;
use Khill\Lavacharts\Configs\BackgroundColor;
use Khill\Lavacharts\Exceptions\InvalidElementId;
use Khill\Lavacharts\Exceptions\InvalidConfigProperty;
use Khill\Lavacharts\Exceptions\InvalidConfigValue;

class Chart
{
    public $type          = null;
    public $label         = null;
    public $datatable     = null;
    public $deferedRender = false;

    protected $defaults  = null;
    protected $events    = [];
    protected $options   = [];

    /**
     * Builds a new chart with a label.
     *
     * @param  string $chartLabel Label for the chart accessed via the Volcano.
     * @param  string $chartLabel Label for the chart accessed via the Volcano.
     * @return void
     */
    public function __construct($chartLabel, $chartDefaults = [])
    {
        $this->label = $chartLabel;
        $this->defaults = array_merge([
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
        ], $chartDefaults);
    }

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
    protected function addOption($o)
    {
        if (is_array($o)) {
            $this->options = array_merge($this->options, $o);
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'array'
            );
        }

        return $this;
    }

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
    public function setOptions($o)
    {
        if (is_array($o) && count($o) > 0) {
            foreach ($o as $option => $value) {
                if (in_array($option, $this->defaults)) {
                    $this->$option($value);
                } else {
                    throw new InvalidConfigProperty(
                        $this->type,
                        __FUNCTION__,
                        $option,
                        $this->defaults
                    );
                }
            }
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'array'
            );
        }
    }

    /**
     * Gets the current chart options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Gets a specific option from the array.
     *
     * @param  string             $o Which option to fetch
     * @throws InvalidConfigValue
     * @return mixed
     */
    public function getOption($o)
    {
        if (is_string($o) && array_key_exists($o, $this->options)) {
            return $this->options[$o];
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string',
                'must be one of '.Utils::arrayToPipedString($this->defaults)
            );
        }
    }

    /**
     * Checks if any events have been assigned to the chart.
     *
     * @return bool
     */
    public function hasEvents()
    {
        return count($this->events) > 0 ? true : false;
    }

    /**
     * Checks if any events have been assigned to the chart.
     *
     * @return bool
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Returns a JSON string representation of the datatable.
     *
     * @since  v2.5.0
     * @return string
     */
    public function getDataTableJson()
    {
        return $this->datatable->toJson();
    }

    /**
     * Assigns wich Datatable will be used for this Chart.
     *
     * @uses  Datatable
     * @param Datatable $datatable
     *
     * @return Chart
     */
    public function datatable(Datatable $datatable)
    {
        $this->datatable = $datatable;

        return $this;
    }

    /**
     * Returns a JSON string representation of the chart's properties.
     *
     * @return string
     */
    public function optionsToJson()
    {
        return json_encode($this->options);
    }

    /**
     * Set up the chart with no datatable to defer rendering via AJAX
     *
     * @since  v2.5.0
     * @param  bool             $dr
     * @throws InvalidElementId
     *
     * @return void
     */
    public function deferedRender($dr)
    {
        if (is_bool($dr)) {
            $this->deferedRender = $dr;
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'bool'
            );
        }

        return $this;
    }

    /**
     * Set the animation options for a chart
     *
     * @param  Animation $animation Animation config object
     * @return Chart
     */
    public function animation(Animation $animation)
    {
        return $this->addOption($animation->toArray());
    }

    /**
     * The background color for the main area of the chart. Can be either a simple
     * HTML color string, for example: 'red' or '#00cc00', or a backgroundColor object
     *
     * @uses  BackgroundColor
     * @param BackgroundColor $backgroundColor
     *
     * @return Chart
     */
    public function backgroundColor(BackgroundColor $backgroundColor)
    {
        return $this->addOption($backgroundColor->toArray());
    }

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
    public function chartArea(ChartArea $ca)
    {
        return $this->addOption($ca->toArray());
    }

    /**
     * The colors to use for the chart elements. An array of strings, where each
     * element is an HTML color string, for example: colors:['red','#004411'].
     *
     * @param  array              $cArr
     * @throws InvalidConfigValue
     *
     * @return Chart
     */
    public function colors($cArr)
    {
        if (Utils::arrayValuesCheck($cArr, 'string')) {
            return $this->addOption([__FUNCTION__ => $cArr]);
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'array',
                'with valid HTML colors'
            );
        }
    }

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
    public function events($e)
    {
        $values = [
            'animationfinish',
            'error',
            'onmouseover',
            'onmouseout',
            'ready',
            'select'
        ];

        if (is_array($e)) {
            foreach ($e as $event) {
                if (is_subclass_of($event, 'Khill\Lavacharts\Events\Event')) {
                    $this->events[] = $event;
                } else {
                    throw $this->invalidConfigValue(
                        __FUNCTION__,
                        'Event'
                    );
                }
            }
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'array'
            );
        }

        return $this;
    }

    /**
     * The default font size, in pixels, of all text in the chart. You can
     * override this using properties for specific chart elements.
     *
     * @param  int                $fontSize
     * @throws InvalidConfigValue
     *
     * @return Chart
     */
    public function fontSize($fontSize)
    {
        if (is_int($fontSize) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return $this->addOption([__FUNCTION__ => $fontSize]);
    }

    /**
     * The default font face for all text in the chart. You can override this
     * using properties for specific chart elements.
     *
     * @param  string             $fontName
     * @throws InvalidConfigValue
     *
     * @return Chart
     */
    public function fontName($fontName)
    {
        if (Utils::nonEmptyString($fontName) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this->addOption([__FUNCTION__ => $fontName]);
    }

    /**
     * Height of the chart, in pixels.
     *
     * @param  int                $h
     * @throws InvalidConfigValue
     *
     * @return Chart
     */
    public function height($h)
    {
        if (is_int($h) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return $this->addOption([__FUNCTION__ => $h]);
    }

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
    public function legend(Legend $l)
    {
        return $this->addOption($l->toArray());
    }

    /**
     * Outputs the chart javascript into the page
     *
     * Pass in a string of the html elementID that you want the chart to be
     * rendered into.
     *
     * @since  v2.0.0
     * @param  string           $elemId The id of an HTML element to render the chart into.
     * @throws InvalidElementId
     *
     * @return string Javscript code blocks
     */
    public function render($elemId)
    {
        $jsf = new JavascriptFactory;

        return $jsf->getChartJs($this, $elemId);
    }

    /**
     * Text to display above the chart.
     *
     * @param  string             $title
     * @throws InvalidConfigValue
     *
     * @return Chart
     */
    public function title($title)
    {
        if (Utils::nonEmptyString($title) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this->addOption([__FUNCTION__ => $title]);
    }

    /**
     * Where to place the chart title, compared to the chart area. Supported values:
     * 'in' - Draw the title inside the chart area.
     * 'out' - Draw the title outside the chart area.
     * 'none' - Omit the title.
     *
     * @param  string             $titlePosition
     * @throws InvalidConfigValue
     *
     * @return Chart
     */
    public function titlePosition($titlePosition)
    {
        $values = array(
            'in',
            'out',
            'none'
        );

        if (in_array($titlePosition, $values, true) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string',
                'with a value of '.Utils::arrayToPipedString($values)
            );
        }

        return $this->addOption([__FUNCTION__ => $titlePosition]);
    }

    /**
     * An object that specifies the title text style. create a new textStyle()
     * object, set the values then pass it to this function or to the constructor.
     *
     * @uses   TextStyle
     * @param  TextStyle          $textStyle
     * @throws InvalidConfigValue
     *
     * @return Chart
     */
    public function titleTextStyle(TextStyle $textStyle)
    {
        return $this->addOption($textStyle->toArray(__FUNCTION__));
    }

    /**
     * An object with members to configure various tooltip elements. To specify
     * properties of this object, create a new tooltip() object, set the values
     * then pass it to this function or to the constructor.
     *
     * @uses   Tooltip
     * @param  Tooltip            $tooltip
     * @throws InvalidConfigValue
     *
     * @return Chart
     */
    public function tooltip(Tooltip $tooltip)
    {
        return $this->addOption($tooltip->toArray());
    }

    /**
     * Width of the chart, in pixels.
     *
     * @param  int                $width
     * @throws InvalidConfigValue
     *
     * @return Chart
     */
    public function width($width)
    {
        if (is_int($width) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return $this->addOption([__FUNCTION__ => $width]);
    }

    /**
     * function for easy creation of exceptions
     */
    protected function invalidConfigValue($func, $type, $extra = '')
    {
        if (! empty($extra)) {
            return new InvalidConfigValue(
                $this->type . '::' . $func,
                $type,
                $extra
            );
        } else {
            return new InvalidConfigValue(
                $this->type . '::' . $func,
                $type
            );
        }
    }
}
