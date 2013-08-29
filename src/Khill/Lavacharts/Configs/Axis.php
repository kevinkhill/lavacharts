<?php namespace Khill\Lavacharts\Configs;
/**
 * Axis Properties Parent Object
 *
 * An object containing all the values for the axis which can be
 * passed into the chart's options.
 *
 *
 * @author Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2013, KHill Designs
 * @link https://github.com/kevinkhill/Codeigniter-gCharts GitHub Repository Page
 * @link http://kevinkhill.github.io/Codeigniter-gCharts/ GitHub Project Page
 * @license http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Helpers\Helpers;

class Axis extends configOptions
{
    /**
     * The baseline for the axis.
     *
     * @var mixed Number or jsDate.
     */
    public $baseline = NULL;

    /**
     * The color of the baseline for the axis.
     *
     * @var string Valid HTML color.
     */
    public $baselineColor = NULL;

    /**
     * The direction in which the values along the axis grow.
     *
     * @var int 1 for natural, -1 for reverse.
     */
    public $direction = NULL;

    /**
     * A format string for numeric axis labels.
     *
     * @var string A string representing how data should be formatted.
     */
    public $format = NULL;

    /**
     * An array with key => value pairs to configure the gridlines.
     *
     * @var array Accepted array keys [ color | count ].
     */
    public $gridlines = NULL;

    /**
     * An array with key => value pairs to configure the minorGridlines.
     *
     * @var array Accepted array keys [ color | count ].
     */
    public $minorGridlines = NULL;

    /**
     * Linear or Logarithmic scaled axis.
     *
     * @var boolean If TRUE, axis will be scaled; If FALSE, linear.
     */
    public $logScale = NULL;

    /**
     * Position of the vertical axis text, relative to the chart area.
     *
     * @var string Accepted values [ out | in | none ].
     */
    public $textPosition = NULL;

    /**
     * An object that specifies the axis text style.
     *
     * @var textStyle
     */
    public $textStyle = NULL;

    /**
     * Property that specifies a title for the axis.
     *
     * @var string Axis title.
     */
    public $title = NULL;

    /**
     * An object that specifies the text style of the chart title.
     *
     * @var textStyle
     */
    public $titleTextStyle = NULL;

    /**
     * Moves the max value of the axis to the specified value.
     *
     * @var int
     */
    public $maxValue = NULL;

    /**
     * Moves the min value of the axis to the specified value.
     *
     * @var int
     */
    public $minValue = NULL;

    /**
     * Specifies how to scale the axis to render the values within the chart area.
     *
     * @var string Accepted values [ pretty | maximized | explicit ].
     */
    public $viewWindowMode = NULL;

    /**
     * Specifies the cropping range of the vertical axis.
     *
     * @var array Accepted array keys [ min | max ].
     */
    public $viewWindow = NULL;

    /**
     * Stores all the configuration for the axis.
     *
     * @var array
     */
    public $options = array();


    /**
     * Builds the configuration when passed an array of options.
     *
     * All options can be set by either passing an array with associative
     * values for option => value, or by chaining together the functions once
     * an object has been created.
     *
     * @param array Associative array containing key => value pairs for the
     * various configuration options.
     * @return \Axis
     */
    public function __construct($config = array())
    {
        $this->options = array_merge($this->options, array(
            'baseline',
            'baselineColor',
            'direction',
            'format',
            'gridlines',
            'minorGridlines',
            'logScale',
            'textPosition',
            'textStyle',
            'title',
            'titleTextStyle',
            'maxAlternation',
            'maxTextLines',
            'minTextSpacing',
            'showTextEvery',
            'maxValue',
            'minValue',
            'viewWindowMode',
            'viewWindow'
        ));

        parent::__construct($config);
    }

    /**
     * Sets the baseline for the axis.
     *
     * This option is only supported for a continuous axis.
     *
     * @param mixed Must match type defined for the column, [ number | jsDate ].
     * @return \Axis
     */
    public function baseline($baseline)
    {
        if(Helpers::is_jsDate($baseline))
        {
            $this->baseline = $baseline->toString();
        } else {
            if(is_int($baseline))
            {
                $this->baseline = $baseline;
            } else {
                $this->type_error(__FUNCTION__, 'int | jsDate', '; int if column is "number", jsDate if column is "date"');
            }
        }

        return $this;
    }

    /**
     * Sets the color of the baseline for the axis.
     *
     * Can be any HTML color string, for example: 'red' or '#00cc00'.
     *
     * This option is only supported for a continuous axis.
     *
     * @param string Valid HTML color.
     * @return \Axis
     */
    public function baselineColor($color)
    {
        if(is_string($color))
        {
            $this->baselineColor = $color;
        } else {
            $this->type_error(__FUNCTION__, 'string', 'representing a valid HTML color');
        }

        return $this;
    }

    /**
     * Sets the direction of the axis values.
     *
     * Specify -1 to reverse the order of the values.
     *
     * @param int $direction
     * @return \Axis
     */
    public function direction($direction)
    {
        if(is_int($direction) && ($direction == 1 || $direction == -1))
        {
            $this->direction = $direction;
        } else {
            $this->type_error(__FUNCTION__, 'int', '1 || -1');
        }

        return $this;
    }

    /**
     * Sets the formatting applied to the axis label. This is a subset of the ICU
     * pattern set. For instance, '#,###%' will display values
     * "1,000%", "750%", and "50%" for values 10, 7.5, and 0.5.
     *
     * For date axis labels, this is a subset of the date formatting ICU pattern
     * set. For instance, "MMM d, y" will display the value
     * "Jul 1, 2011" for the date of July first in 2011.
     *
     * This option is only supported for a continuous axis.
     *
     * @param string $format format string for numeric or date axis labels.
     * @return \Axis
     */
    public function format($format)
    {
        if(is_string($format))
        {
            $this->format = $format;
        } else {
            $this->type_error(__FUNCTION__, 'string');
        }

        return $this;
    }

    /**
     * Sets the color and count of the gridlines.
     *
     * 'color' - The color of the gridlines, Specify a valid HTML color string.
     * 'count' - The number of horizontal gridlines inside the chart area.
     * Minimum value is 2. Specify -1 to automatically compute the number
     * of gridlines.
     * array('color' => '#333', 'count' => 4);
     *
     * This option is only supported for a continuous axis.
     *
     * @param array $gridlines
     * @return \Axis
     */
    public function gridlines($gridlines)
    {
        $tmp = array();

        if(is_array($gridlines))
        {
            if(array_key_exists('count', $gridlines))
            {
                if($gridlines['count'] >= 2 || $gridlines['count'] == -1)
                {
                    $tmp['count'] = $gridlines['count'];
                } else {
                    $tmp['count'] = 5;
                }
            } else {
                $tmp['count'] = 5;
            }

            if(array_key_exists('color', $gridlines))
            {
                $tmp['color'] = $gridlines['color'];
            } else {
                $tmp['color'] = '#CCC';
            }

            $this->gridlines = $tmp;
        } else {
            $this->type_error(__FUNCTION__, 'array', 'with keys for count & color');
        }

        return $this;
    }

    /**
     * Sets the color and count of the minorGridlines
     *
     * 'color' - The color of the minor gridlines inside the chart area,
     * specify a valid HTML color string.
     * 'count' - The number of minor gridlines between two regular gridlines.
     *
     * This option is only supported for a continuous axis.
     *
     * @param array $minorGridlines
     * @return \Axis
     */
    public function minorGridlines($minorGridlines)
    {
       $tmp = array();

        if(is_array($minorGridlines))
        {
            if(array_key_exists('count', $minorGridlines) &&
                    $minorGridlines['count'] >= 2 ||
                    $minorGridlines['count'] == -1
            ) {
                $tmp['count'] = $minorGridlines['count'];
            } else {
                $this->type_error(__FUNCTION__.'[count]',  'int', '>= 2 or -1 for auto');
            }

            if(array_key_exists('color', $minorGridlines))
            {
                $tmp['color'] = $minorGridlines['color'];
            }

            $this->minorGridlines = $tmp;
        } else {
            $this->type_error(__FUNCTION__, 'array', 'with keys count & color');
        }

        return $this;
    }

    /**
     * Sets the axis property that makes the axis a logarithmic scale
     * (requires all values to be positive). Set to [ TRUE | FALSE ].
     *
     * This option is only supported for a continuous axis.
     *
     * @param boolean $log
     * @return \Axis
     */
    public function logScale($log)
    {
        if(is_bool($log))
        {
            $this->logScale = $log;
        } else {
            $this->type_error(__FUNCTION__, 'boolean');
        }

        return $this;
    }

    /**
     * Position of the axis text, relative to the chart area.
     * Supported values: 'out', 'in', 'none'.
     *
     * @param string Setting the position of the text.
     * @return \Axis
     */
    public function textPosition($position)
    {
        $values = array(
            'out',
            'in',
            'none'
        );

        if(in_array($position, $values))
        {
            $this->textPosition = $position;
        } else {
            $this->type_error(__FUNCTION__, 'string', 'with a value of '.Helpers::array_string($values));
        }

        return $this;
    }

    /**
     * This function takes a textStyle object, created via "new textStyle();"
     *
     * @param textStyle $textStyle
     * @return \Axis
     */
    public function textStyle($textStyle)
    {
        if(Helpers::is_textStyle($textStyle))
        {
            $this->textStyle = $textStyle->values();
        } else {
            $this->type_error(__FUNCTION__, 'object', 'class textStyle');
        }

        return $this;
    }

    /**
     * Axis property that specifies the title of the axis.
     *
     * @param string $title
     * @return \Axis
     */
    public function title($title)
    {
        if(is_string($title))
        {
            $this->title = $title;
        } else {
            $this->type_error(__FUNCTION__, 'string');
        }

        return $this;
    }

    /**
     * An object that specifies the axis title text style.
     *
     * @param textStyle $titleTextStyle
     * @return \Axis
     */
    public function titleTextStyle($titleTextStyle)
    {
        if(Helpers::is_textStyle($titleTextStyle))
        {
            $this->titleTextStyle = $titleTextStyle->values();
        } else {
            $this->type_error(__FUNCTION__, 'object', 'class textStyle');
        }

        return $this;
    }

    /**
     * Maximum number of levels of axis text. If axis text labels
     * become too crowded, the server might shift neighboring labels up or down
     * in order to fit labels closer together. This value specifies the most
     * number of levels to use; the server can use fewer levels, if labels can
     * fit without overlapping.
     *
     * This option is only supported for a discrete axis.
     *
     * @param int $alternation
     * @return \Axis
     */
    public function maxAlternation($alternation)
    {
        if(is_int($alternation))
        {
            $this->maxAlternation = $alternation;
        } else {
            $this->type_error(__FUNCTION__, 'int');
        }

        return $this;
    }

    /**
     * Maximum number of lines allowed for the text labels. Labels can span
     * multiple lines if they are too long, and the nuber of lines is, by
     * default, limited by the height of the available space.
     *
     * This option is only supported for a discrete axis.
     *
     * @param int $maxTextLines
     * @return \Axis
     */
    public function maxTextLines($maxTextLines)
    {
        if(is_int($maxTextLines))
        {
            $this->maxTextLines = $maxTextLines;
        } else {
            $this->type_error(__FUNCTION__, 'int');
        }

        return $this;
    }

    /**
     * Minimum spacing, in pixels, allowed between two adjacent text
     * labels. If the labels are spaced too densely, or they are too long,
     * the spacing can drop below this threshold, and in this case one of the
     * label-unclutter measures will be applied (e.g, truncating the lables or
     * dropping some of them).
     *
     * This option is only supported for a discrete axis.
     *
     * @param int $minTextSpacing
     * @return \Axis
     */
    public function minTextSpacing($minTextSpacing)
    {
        if(is_int($minTextSpacing))
        {
            $this->minTextSpacing = $minTextSpacing;
        } else {
            if(isset($this->textStyle['fontSize']))
            {
                $this->minTextSpacing = $this->textStyle['fontSize'];
            } else {
                $this->type_error(__FUNCTION__, 'int');
            }
        }

        return $this;
    }

    /**
     * How many axis labels to show, where 1 means show every label,
     * 2 means show every other label, and so on. Default is to try to show as
     * many labels as possible without overlapping.
     *
     * This option is only supported for a discrete axis.
     *
     * @param int $showTextEvery
     * @return \Axis
     */
    public function showTextEvery($showTextEvery)
    {
        if(is_int($showTextEvery))
        {
            $this->showTextEvery = $showTextEvery;
        } else {
            $this->type_error(__FUNCTION__, 'int');
        }

        return $this;
    }

    /**
     * axis property that specifies the highest axis grid line. The
     * actual grid line will be the greater of two values: the maxValue option
     * value, or the highest data value, rounded up to the next higher grid mark.
     *
     * This option is only supported for a continuous axis.
     *
     * @param int $max
     * @return \Axis
     */
    public function maxValue($max)
    {
        if(is_int($max))
        {
            $this->maxValue = $max;
        } else {
            $this->type_error(__FUNCTION__, 'int');
        }

        return $this;
    }

    /**
     * axis property that specifies the lowest axis grid line. The
     * actual grid line will be the lower of two values: the minValue option
     * value, or the lowest data value, rounded down to the next lower grid mark.
     *
     * This option is only supported for a continuous axis.
     *
     * @param int $min
     * @return \Axis
     */
    public function minValue($min)
    {
        if(is_int($min))
        {
            $this->minValue = $min;
        } else {
           $this->type_error(__FUNCTION__, 'int');
        }

        return $this;
    }

    /**
     * Specifies how to scale the axis to render the values within
     * the chart area. The following string values are supported:
     *
     * 'pretty' - Scale the values so that the maximum and minimum
     * data values are rendered a bit inside the left and right of the chart area.
     * 'maximized' - Scale the values so that the maximum and minimum
     * data values touch the left and right of the chart area.
     * 'explicit' - Specify the left and right scale values of the chart area.
     * Data values outside these values will be cropped. You must specify an
     * axis.viewWindow array describing the maximum and minimum values to show.
     *
     * This option is only supported for a continuous axis.
     *
     * @param string $viewMode
     * @return \Axis
     */
    public function viewWindowMode($viewMode)
    {
        $values = array(
            'pretty',
            'maximized',
            'explicit',
        );

        if(in_array($viewMode, $values))
        {
            $this->viewWindowMode = $viewMode;
        } else {
            if($this->viewWindow == NULL)
            {
                $this->viewWindowMode = 'pretty';
            } else {
                $this->viewWindowMode = 'explicit';
            }
        }

        return $this;
    }

    /**
     * Specifies the cropping range of the axis.
     *
     * For a continuous axis:
     * The minimum and maximum data value to render. Has an effect
     * only if $this->viewWindowMode = 'explicit'.
     *
     * For a discrete axis:
     * 'min' - The zero-based row index where the cropping window begins. Data
     * points at indices lower than this will be cropped out. In conjunction with
     * vAxis->viewWindow['max'], it defines a half-opened range (min, max)
     * that denotes the element indices to display. In other words, every index
     * such that min <= index < max will be displayed.
     *
     * 'max' - The zero-based row index where the cropping window ends. Data
     * points at this index and higher will be cropped out. In conjunction with
     * vAxis->viewWindow['min'], it defines a half-opened range (min, max)
     * that denotes the element indices to display. In other words, every index
     * such that min <= index < max will be displayed.
     *
     * @param array $viewWindow
     * @return \Axis
     */
    public function viewWindow($viewWindow)
    {
        $tmp = array();

        if(is_array($viewWindow))
        {
            if(array_key_exists('min', $viewWindow) && array_key_exists('max', $viewWindow))
            {
                $tmp['viewWindowMin'] = $viewWindow['min'];
                $tmp['viewWindowMax'] = $viewWindow['max'];

                $this->viewWindowMode = 'explicit';
            } else {
                $tmp['viewWindowMin'] = NULL;
                $tmp['viewWindowMax'] = NULL;
            }

            $this->viewWindow = $tmp;
        } else {
            $this->type_error(__FUNCTION__, 'array', 'with keys min && max');
        }

        return $this;
    }

}
