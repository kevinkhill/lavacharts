<?php namespace Khill\Lavacharts\Charts;

/**
 * Chart Class, Parent to all charts.
 *
 * Has common properties and methods used between all the different charts.
 *
 *
 * @category  Class
 * @package   Khill\Lavacharts\Charts
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2014, KHill Designs
 * @link      https://github.com/kevinkhill/LavaCharts GitHub Repository Page
 * @link      http://kevinkhill.github.io/LavaCharts/ GitHub Project Page
 * @license   http://www.gnu.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Configs\DataTable;
use Khill\Lavacharts\JavascriptFactory;
use Khill\Lavacharts\Helpers\Helpers;
use Khill\Lavacharts\Configs\Tooltip;
use Khill\Lavacharts\Configs\TextStyle;
use Khill\Lavacharts\Configs\ChartArea;
use Khill\Lavacharts\Configs\BackgroundColor;
use Khill\Lavacharts\Exceptions\LabelNotFound;
use Khill\Lavacharts\Exceptions\InvalidElementId;
use Khill\Lavacharts\Exceptions\InvalidConfigValue;

class Chart
{
    public $type       = null;
    public $label      = null;
    public $dataTable  = null;
    public $events     = null;
    public $defaults   = null;
    public $options    = array();

    /**
     * Builds a new chart with a label and access to the volcano storage
     *
     * @param string $chartLabel Label for the chart to be stored in the Volcano. 
     */
    public function __construct($chartLabel)
    {
        $this->label = $chartLabel;
        $this->defaults = array(
            'dataTable',
            'backgroundColor',
            'chartArea',
            'colors',
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
        );
    }

    /**
     * Sets configuration options from array of values
     *
     * You can set the options all at once instead of passing them individually
     * or chaining the functions from the chart objects.
     *
     * @param array $options
     *
     * @return Khill\Lavacharts\Charts\Chart
     */
    public function setConfig($options = array())
    {
        if (is_array($options) && count($options) > 0) {
            foreach ($options as $option => $value) {
                if (in_array($option, $this->defaults)) {
                    if (method_exists($this, $option)) {
                        $this->$option($value);
                    } else {
                        $this->addOption($value);
                    }
                } else {
                    $this->error('Invalid config value "'.$option.'", must be an option from this list '.Helpers::arrayToPipedString($this->defaults));
                }
            }
        } else {
            $this->error('Invalid value for setConfig, must be type (array) containing a minimum of one key from '.Helpers::arrayToPipedString($this->defaults));
        }

        return $this;
    }

    /**
     * Sets a configuration option
     *
     * Takes an array with option => value, or an object created by
     * one of the configOptions child objects.
     *
     * @param mixed $option
     *
     * @return Khill\Lavacharts\Charts\Chart
     */
    public function addOption($option)
    {
        switch(gettype($option))
        {
            case 'object':
                $this->options = array_merge($this->options, $option->toArray());
                break;

            case 'array':
                $this->options = array_merge($this->options, $option);
                break;

            default:
                throw new \Exception('Invalid option, must be type (object|array)');
                break;
        }

        return $this;
    }

    /**
     * Assigns wich DataTable will be used for this Chart.
     *
     * If a label is provided then the defined DataTable will be used.
     * If called with no argument, or the chart is attempted to be generated
     * without calling this function, the chart will search for a DataTable with
     * the same label as the Chart.
     *
     * @param Khill\Lavacharts\Configs\DataTable
     *
     * @return Khill\Lavacharts\Charts\Chart
     */

    public function dataTable(DataTable $dataTable)
    {
        $this->dataTable = $dataTable;

        return $this;
    }

    /**
     * Outputs the chart javascript into the page
     *
     * Pass in a string of the html elementID that you want the chart to be
     * rendered into.
     *
     * @param string $elementId The id of an HTML element to render the chart into.
     * @throws Khill\Lavacharts\Exceptions\InvalidElementId
     *
     * @return string Javscript code blocks
     */
    public function outputInto($elementId)
    {
        $jsf = new JavascriptFactory($this, $elementId);

        return $jsf->buildOutput();
    }

    /**
     * The background color for the main area of the chart. Can be either a simple
     * HTML color string, for example: 'red' or '#00cc00', or a backgroundColor object
     *
     * @param Khill\Lavacharts\Configs\BackgroundColor $backgroundColor
     *
     * @return Khill\Lavacharts\Charts\Chart
     */
    public function backgroundColor(BackgroundColor $backgroundColor)
    {
        $this->addOption($backgroundColor->toArray());

        return $this;
    }

    /**
     * An object with members to configure the placement and size of the chart area
     * (where the chart itself is drawn, excluding axis and legends).
     * Two formats are supported: a number, or a number followed by %.
     * A simple number is a value in pixels; a number followed by % is a percentage.
     *
     * @param Khill\Lavacharts\Configs\ChartArea $chartArea
     *
     * @return Khill\Lavacharts\Charts\Chart
     */
    public function chartArea(ChartArea $chartArea)
    {
        $this->addOption($chartArea->toArray());
 
        return $this;
    }

    /**
     * The colors to use for the chart elements. An array of strings, where each
     * element is an HTML color string, for example: colors:['red','#004411'].
     *
     * @param array $colorArray
     *
     * @return Khill\Lavacharts\Charts\Chart
     */
    public function colors($colorArray)
    {
        if (Helpers::arrayValuesCheck($colorArray, 'string')) {
            $this->addOption(array('colors' => $colorArray));
        } else {
            $this->type_error(__FUNCTION__, 'array', 'with valid HTML colors');
        }

        return $this;
    }

    /**
     * Register javascript callbacks for specific events.
     *
     * Valid values include:
     * [ animationfinish | error | onmouseover | onmouseout | ready | select ]
     * associated to a respective pre-defined javascript function as the callback.
     *
     * @param array $events Array of events associated to a callback
     *
     * @return Khill\Lavacharts\Charts\Chart
     */
    public function events($events)
    {
        $values = array(
            'animationfinish',
            'error',
            'onmouseover',
            'onmouseout',
            'ready',
            'select'
        );

        if (is_array($events)) {
            foreach ($events as $event) {
                if (in_array($event, $values)) {
                    $this->events[] = $event;
                } else {
                    $this->error('Invalid events array key value, must be (string) with any key '.Helpers::arrayToPipedString($values));
                }
            }
        } else {
            $this->type_error(__FUNCTION__, 'array', 'containing any key '.Helpers::arrayToPipedString($values));
        }

        return $this;
    }

    /**
     * The default font size, in pixels, of all text in the chart. You can
     * override this using properties for specific chart elements.
     *
     * @param int $fontSize
     *
     * @return Khill\Lavacharts\Charts\Chart
     */
    public function fontSize($fontSize)
    {
        if (is_int($fontSize)) {
            $this->addOption(array('fontSize' => $fontSize));
        } else {
            $this->type_error(__FUNCTION__, 'int');
        }

        return $this;
    }

    /**
     * The default font face for all text in the chart. You can override this
     * using properties for specific chart elements.
     *
     * @param string $fontName
     *
     * @return Khill\Lavacharts\Charts\Chart
     */
    public function fontName($fontName)
    {
        if (is_string($fontName)) {
            $this->addOption(array('fontName' => $fontName));
        } else {
            $this->error(__FUNCTION__, 'string');
        }

        return $this;
    }

    /**
     * Height of the chart, in pixels.
     *
     * @param int $height
     *
     * @return Khill\Lavacharts\Charts\Chart
     */
    public function height($height)
    {
        if (is_int($height)) {
            $this->addOption(array('height' => $height));
        } else {
            $this->type_error(__FUNCTION__, 'int');
        }

        return $this;
    }

    /**
     * An object with members to configure various aspects of the legend. To
     * specify properties of this object, create a new legend() object, set the
     * values then pass it to this function or to the constructor.
     *
     * @param Khill\Lavacharts\Configs\Legend $legend
     *
     * @return Khill\Lavacharts\Charts\Chart
     */
    public function legend(Legend $legend)
    {
        $this->addOption($legend->toArray());
 
        return $this;
    }

    /**
     * Text to display above the chart.
     *
     * @param string $title
     *
     * @return Khill\Lavacharts\Charts\Chart
     */
    public function title($title)
    {
        if (is_string($title)) {
            $this->addOption(array('title' => $title));
        } else {
            $this->type_error(__FUNCTION__, 'string');
        }

        return $this;
    }

    /**
     * Where to place the chart title, compared to the chart area. Supported values:
     * 'in' - Draw the title inside the chart area.
     * 'out' - Draw the title outside the chart area.
     * 'none' - Omit the title.
     *
     * @param string $position
     *
     * @return Khill\Lavacharts\Charts\Chart
     */
    public function titlePosition($position)
    {
        $values = array(
            'in',
            'out',
            'none'
        );

        if (in_array($position, $values)) {
            $this->addOption(array('titlePosition' => $position));
        } else {
            $this->type_error(__FUNCTION__, 'string', 'with a value of '.Helpers::arrayToPipedString($values));
        }

        return $this;
    }

    /**
     * An object that specifies the title text style. create a new textStyle()
     * object, set the values then pass it to this function or to the constructor.
     *
     * @param Khill\Lavacharts\Configs\TextStyle $textStyle
     *
     * @return Khill\Lavacharts\Charts\Chart
     */
    public function titleTextStyle(TextStyle $textStyle)
    {
        $this->addOption(array('titleTextStyle' => $textStyle->getValues()));           

        return $this;
    }


    /**
     * An object with members to configure various tooltip elements. To specify
     * properties of this object, create a new tooltip() object, set the values
     * then pass it to this function or to the constructor.
     *
     * @param Khill\Lavacharts\Configs\Tooltip $tooltip
     *
     * @return Khill\Lavacharts\Charts\Chart
     */
    public function tooltip(Tooltip $tooltip)
    {
        $this->addOption($tooltip->toArray());
 
        return $this;
    }

    /**
     * Width of the chart, in pixels.
     *
     * @param int $width
     *
     * @return Khill\Lavacharts\Charts\Chart
     */
    public function width(int $width)
    {
        if (is_int($width)) {
            $this->addOption(array('width' => $width));
        } else {
            $this->type_error(__FUNCTION__, 'int');
        }

        return $this;
    }

    /**
     * Returns a JSON string representation of the object's properties.
     *
     * @return string
     */
    public function optionsToJson()
    {
        return json_encode($this->options);
    }

    protected function invalidConfigValue($func, $type, $extra = '')
    { 
        if ( ! empty($extra)) {
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
