<?php namespace Khill\Lavacharts\Charts;
/**
 * Chart Class, Parent to all charts.
 *
 * Has common properties and methods used between all the different charts.
 *
 *
 * @author Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2013, KHill Designs
 * @link https://github.com/kevinkhill/Codeigniter-gCharts Github Page
 * @license http://www.gnu.org/licenses/gpl.html GPL-V3
 *
 */

use Khill\Lavacharts\Lavacharts;
use Khill\Lavacharts\Helpers\Helpers;

class Chart
{
    public $chartType = NULL;
    public $chartLabel = NULL;
    public $dataTable = NULL;

    public $data = NULL;
    public $options = NULL;
    public $defaults = NULL;
    public $events = NULL;
    public $elementID = NULL;

    public function __construct($chartLabel)
    {
        $typePieces = explode('\\', get_class($this));

        $this->chartType = $typePieces[count($typePieces) - 1];
        $this->chartLabel = $chartLabel;
        $this->options = array();
        $this->defaults = array(
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
     * @return \Chart
     */
    public function setConfig($options = array())
    {
        if(is_array($options) && count($options) > 0)
        {
            foreach($options as $option => $value)
            {
                if(in_array($option, $this->defaults))
                {
                    if(method_exists($this, $option))
                    {
                        $this->$option($value);
                    } else {
                        $this->addOption($value);
                    }
                } else {
                    $this->error('Invalid config value "'.$option.'", must be an option from this list '.Helpers::array_string($this->defaults));
                }
            }
        } else {
            $this->error('Invalid value for setConfig, must be type (array) containing a minimum of one key from '.Helpers::array_string($this->defaults));
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
     * @return \Chart
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
                $this->type_error(__FUNCTION__, 'object | array', ', option != '.print_r($option));
            break;
        }

        return $this;
    }

    /**
     * Assigns wich DataTable will be used for this Chart. If a label is provided
     * then the defined DataTable will be used. If called with no argument, it will
     * attempt to use a DataTable with the same label as the Chart.
     *
     * @param mixed dataTableLabel String label or DataTable object
     * @return \DataTable DataTable object
     */
    public function dataTable($data = NULL)
    {
        switch(gettype($data))
        {
            case 'object':
                if(get_class($data) == 'DataTable')
                {
                    $this->data = $data;
                    $this->dataTable = 'local';
                } else {
                    Lavacharts::_set_error(get_class($this), 'Invalid dataTable object, must be type (DataTable).');
                }
            break;

            case 'string':
                if($data != '')
                {
                    $this->dataTable = $data;
                } else {
                    Lavacharts::_set_error(get_class($this), 'Invalid dataTable label, must be type (string) non-empty.');
                }
            break;

            default:
                $this->dataTable = $this->chartLabel;
            break;
        }

        return $this;
    }

    /**
     * The background color for the main area of the chart. Can be either a simple
     * HTML color string, for example: 'red' or '#00cc00', or a backgroundColor object
     *
     * @param $backgroundColor backgroundColor
     * @return \Chart
     */
    public function backgroundColor($backgroundColor)
    {
        if(Helpers::is_backgroundColor($backgroundColor))
        {
            $this->addOption($backgroundColor->toArray());
        } else {
            $this->type_error(__FUNCTION__, 'backgroundColor');
        }

        return $this;
    }

    /**
     * An object with members to configure the placement and size of the chart area
     * (where the chart itself is drawn, excluding axis and legends).
     * Two formats are supported: a number, or a number followed by %.
     * A simple number is a value in pixels; a number followed by % is a percentage.
     *
     * @param chartArea $chartArea
     * @return \Chart
     */
    public function chartArea($chartArea)
    {
        if(Helpers::is_chartArea($chartArea))
        {
            $this->addOption($chartArea->toArray());
        } else {
            $this->type_error(__FUNCTION__, 'chartArea');
        }

        return $this;
    }

    /**
     * The colors to use for the chart elements. An array of strings, where each
     * element is an HTML color string, for example: colors:['red','#004411'].
     *
     * @param array $colorArray
     * @return \Chart
     */
    public function colors($colorArray)
    {
        if(is_array($colorArray))
        {
            $this->addOption(array('colors' => $colorArray));
        } else {
            $this->type_error(__FUNCTION__, 'array', 'with valid HTML colors');
        }

        return $this;
    }

    /**
     * Register javascript callbacks for specific events. Valid values include
     * [ animationfinish | error | onmouseover | onmouseout | ready | select ]
     * associated to a respective pre-defined javascript function as the callback.
     *
     * @param array $events Array of events associated to a callback
     * @return \Chart
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

        if(is_array($events))
        {
            foreach($events as $event)
            {
                if(in_array($event, $values))
                {
                    $this->events[] = $event;
                } else {
                    $this->error('Invalid events array key value, must be (string) with any key '.Helpers::array_string($values));
                }
            }
        } else {
            $this->type_error(__FUNCTION__, 'array', 'containing any key '.Helpers::array_string($values));
        }

        return $this;
    }

    /**
     * The default font size, in pixels, of all text in the chart. You can
     * override this using properties for specific chart elements.
     *
     * @param int $fontSize
     * @return \Chart
     */
    public function fontSize($fontSize)
    {
        if(is_int($fontSize))
        {
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
     * @return \Chart
     */
    public function fontName($fontName)
    {
        if(is_string($fontName))
        {
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
     * @return \Chart
     */
    public function height($height)
    {
        if(is_int($height))
        {
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
     * @param legend $legendObj
     * @return \AreaChart
     */
    public function legend($legendObj)
    {
        if(Helpers::is_legend($legendObj))
        {
            $this->addOption($legendObj->toArray());
        } else {
            $this->type_error(__FUNCTION__, 'legend');
        }

        return $this;
    }

    /**
     * Text to display above the chart.
     *
     * @param string $title
     * @return \Chart
     */
    public function title($title)
    {
        if(is_string($title))
        {
            $this->addOption(array('title' => (string) $title));
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
     * @return \Chart
     */
    public function titlePosition($position)
    {
        $values = array(
            'in',
            'out',
            'none'
        );

        if(in_array($position, $values))
        {
            $this->addOption(array('titlePosition' => $position));
        } else {
            $this->type_error(__FUNCTION__, 'string', 'with a value of '.Helpers::array_string($values));
        }

        return $this;
    }

    /**
     * An object that specifies the title text style. create a new textStyle()
     * object, set the values then pass it to this function or to the constructor.
     *
     * @param textStyle $textStyleObj
     * @return \Chart
     */
    public function titleTextStyle($textStyleObj)
    {
        if(Helpers::is_textStyle($textStyleObj))
        {
            $this->addOption(array('titleTextStyle' => $textStyleObj->values()));
        } else {
            $this->type_error(__FUNCTION__, 'textStyle');
        }

        return $this;
    }


    /**
     * An object with members to configure various tooltip elements. To specify
     * properties of this object, create a new tooltip() object, set the values
     * then pass it to this function or to the constructor.
     *
     * @param tooltip $tooltipObj
     * @return \Chart
     */
    public function tooltip($tooltipObj)
    {
        if(Helpers::is_tooltip($tooltipObj))
        {
            $this->addOption($tooltipObj->toArray());
        } else {
            $this->error(__FUNCTION__, 'tooltip');
        }

        return $this;
    }

    /**
     * Width of the chart, in pixels.
     *
     * @param int $width
     * @return \Chart
     */
    public function width($width)
    {
        if(is_int($width))
        {
            $this->addOption(array('width' => $width));
        } else {
            $this->type_error(__FUNCTION__, 'int');
        }

        return $this;
    }

    /**
     * Adds the error message to the error log in the lavacharts master object.
     *
     * @param string $msg
     */
    public function error($msg)
    {
        Lavacharts::_set_error($this->chartType.'('.$this->chartLabel.')', $msg);
    }

    /**
     * Adds an function/type error message to the error log in the lavacharts object.
     *
     * @param string Property in error
     * @param string Variable type
     * @param string Extra message to append to error
     */
    public function type_error($val, $type, $extra = FALSE)
    {
        $msg = sprintf(
            'Invalid value for %s, must be type (%s)',
            $val,
            $type
        );

        $msg .= $extra ? ' '.$extra.'.' : '.';

        $this->error($msg);
    }

    /**
     * Outputs the chart javascript into the page
     *
     * Pass in a string of the html elementID that you want the chart to be
     * rendered into. Plus, if the dataTable function was never called on the
     * chart to assign a DataTable to use, it will automatically attempt to use
     * a DataTable with the same label as the chart.
     *
     * @param string $elementID
     * @return string Javscript code blocks
     */
    public function outputInto($elementID = NULL)
    {
        if($this->dataTable === NULL)
        {
            $this->dataTable = $this->chartLabel;
        }

        if(gettype($elementID) == 'string' && $elementID != NULL)
        {
            $this->elementID = $elementID;
        }

        return Lavacharts::_build_script_block($this);
    }

    /**
     * Returns a JSON string representation of the object's properties.
     *
     * @return string
     */
    public function optionsToJSON()
    {
        return json_encode($this->options);
    }

}
