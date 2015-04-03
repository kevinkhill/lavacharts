<?php namespace Khill\Lavacharts\Configs;

/**
 * Axis Properties Parent Object
 *
 * An object containing all the values for the axis which can be
 * passed into the chart's options.
 *
 *
 * @package    Lavacharts
 * @subpackage Configs
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Utils;
use Khill\Lavacharts\Exceptions\InvalidConfigValue;

class Axis extends ConfigObject
{
    /**
     * The baseline for the axis.
     *
     * @TODO: FIX THIS
     * @var int|Carbon
     */
    public $baseline;

    /**
     * The color of the baseline for the axis.
     *
     * @var string Valid HTML color.
     */
    public $baselineColor;

    /**
     * The direction in which the values along the axis grow.
     *
     * @var int 1 for natural, -1 for reverse.
     */
    public $direction;

    /**
     * A format string for numeric axis labels.
     *
     * @var string A string representing how data should be formatted.
     */
    public $format;

    /**
     * An array with key => value pairs to configure the gridlines.
     *
     * @var array Accepted array keys [ color | count ].
     */
    public $gridlines;

    /**
     * Linear or Logarithmic scaled axis.
     *
     * @var bool If true, axis will be scaled; If false, linear.
     */
    public $logScale;

    /**
     * Moves the max value of the axis to the specified value.
     *
     * @var int
     */
    public $maxAlternation;

    /**
     * Maximum number of lines allowed for the text labels.
     *
     * @var int
     */
    public $maxTextLines;

    /**
     * Moves the max value of the axis to the specified value.
     *
     * @var int
     */
    public $maxValue;

    /**
     * An array with key => value pairs to configure the minorGridlines.
     *
     * @var array Accepted array keys [ color | count ].
     */
    public $minorGridlines;

    /**
     * Minimum spacing, in pixels, allowed between two adjacent text labels.
     *
     * @var int
     */
    public $minTextSpacing;

    /**
     * Moves the min value of the axis to the specified value.
     *
     * @var int
     */
    public $minValue;

    /**
     * How many axis labels to show.
     *
     * @var int
     */
    public $showTextEvery;

    /**
     * Position of the vertical axis text, relative to the chart area.
     *
     * @var string Accepted values [ out | in | none ].
     */
    public $textPosition;

    /**
     * An object that specifies the axis text style.
     *
     * @var TextStyle
     */
    public $textStyle;

    /**
     * Property that specifies a title for the axis.
     *
     * @var string Axis title.
     */
    public $title;

    /**
     * An object that specifies the text style of the chart title.
     *
     * @var textStyle
     */
    public $titleTextStyle;

    /**
     * Specifies the cropping range of the vertical axis.
     *
     * @var array Accepted array keys [ min | max ].
     */
    public $viewWindow;

    /**
     * Specifies how to scale the axis to render the values within the chart area.
     *
     * @var string Accepted values [ pretty | maximized | explicit ].
     */
    public $viewWindowMode;


    /**
     * Builds the configuration when passed an array of options.
     *
     * All options can be set by either passing an array with associative
     * values for option => value, or by chaining together the functions once
     * an object has been created.
     *
     * @param  VerticalAxis|HorizontalAxis $child  The axis object
     * @param  array                       $config Associative array containing key => value pairs for the various configuration options.
     * @throws InvalidConfigValue
     * @throws InvalidConf
     * @throws InvalidConfigValuerty
     * @return Axis
     */
    public function __construct($child, $config)
    {
        parent::__construct($child, $config);
    }

    /**
     * Sets the baseline for the axis.
     *
     * This option is only supported for a continuous axis.
     *
     * @param  mixed Must match type defined for the column, [ number | jsDate ].
     * @throws InvalidConfigValue
     * @return Axis
     */
    public function baseline($baseline) //@TODO convert to carbon
    {
        if (Utils::isJsDate($baseline)) {
            $this->baseline = $baseline->toString();
        } else {
            if (is_int($baseline)) {
                $this->baseline = $baseline;
            } else {
                throw $this->invalidConfigValue(
                    __FUNCTION__,
                    'int | jsDate',
                    'int if column is "number", jsDate if column is "date"'
                );
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
     * @param  string Valid HTML color.
     * @throws InvalidConfigValue
     * @return Axis
     */
    public function baselineColor($color)
    {
        if (is_string($color)) {
            $this->baselineColor = $color;
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string',
                'representing a valid HTML color'
            );
        }

        return $this;
    }

    /**
     * Sets the direction of the axis values.
     *
     * specify 1 for normal, -1 to reverse the order of the values.
     *
     * @param  int $direction
     * @throws InvalidConfigValue
     * @return Axis
     */
    public function direction($direction)
    {
        if (is_int($direction) && ($direction == 1 || $direction == -1)) {
            $this->direction = $direction;
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int',
                '1 || -1'
            );
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
     * @param  string $format format string for numeric or date axis labels.
     * @throws InvalidConfigValue
     * @return Axis
     */
    public function format($format)
    {
        if (is_string($format)) {
            $this->format = $format;
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string'
            );
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
     * @param  array $gridlines
     * @throws InvalidConfigValue
     * @return Axis
     */
    public function gridlines($gridlines)
    {
        if (is_array($gridlines) && array_key_exists('count', $gridlines) && array_key_exists('color', $gridlines)) {
            if (Utils::nonEmptyString($gridlines['color'])) {
                $this->gridlines['color'] = $gridlines['color'];
            } else {
                throw $this->invalidConfigValue(
                    __FUNCTION__,
                    'array',
                    'with the value of the key "color" being a valid HTML color'
                );
            }

            if (is_int($gridlines['count']) && $gridlines['count'] >= 2 || $gridlines['count'] == -1) {
                $this->gridlines['count'] = $gridlines['count'];
            } else {
                throw $this->invalidConfigValue(
                    __FUNCTION__,
                    'array',
                    'with the value of the key "count" == -1 || >= 2'
                );
            }
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'array',
                'with keys for count & color'
            );
        }

        return $this;
    }

    /**
     * Sets the axis property that makes the axis a logarithmic scale
     * (requires all values to be positive). Set to [ true | false ].
     *
     * This option is only supported for a continuous axis.
     *
     * @param  bool $logScale
     * @throws InvalidConfigValue
     * @return Axis
     */
    public function logScale($logScale)
    {
        if (is_bool($logScale)) {
            $this->logScale = $logScale;
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'bool'
            );
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
     * @param  array $minorGridlines
     * @throws InvalidConfigValue
     * @return Axis
     */
    public function minorGridlines($minorGridlines)
    {
        if (is_array($minorGridlines) && array_key_exists('count', $minorGridlines) && array_key_exists('color', $minorGridlines)) {
            if (Utils::nonEmptyString($minorGridlines['color'])) {
                $this->minorGridlines['color'] = $minorGridlines['color'];
            } else {
                throw $this->invalidConfigValue(
                    __FUNCTION__,
                    'array',
                    'with the value of the key "color" being a valid HTML color'
                );
            }

            if (is_int($minorGridlines['count']) && $minorGridlines['count'] >= 2 || $minorGridlines['count'] == -1) {
                $this->minorGridlines['count'] = $minorGridlines['count'];
            } else {
                throw $this->invalidConfigValue(
                    __FUNCTION__,
                    'array',
                    'with the value of the key "count" == -1 || >= 2'
                );
            }
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'array',
                'with keys for count & color'
            );
        }

        return $this;
    }

    /**
     * Maximum number of levels of axis text.
     *
     * If axis text labels become too crowded, the server might
     * shift neighboring labels up or down in order to fit labels
     * closer together. This value specifies the most number of
     * levels to use; the server can use fewer levels, if labels
     * can fit without overlapping.
     *
     * This option is only supported for a discrete axis.
     *
     * @param  int $alternation
     * @throws InvalidConfigValue
     * @return Axis
     */
    public function maxAlternation($alternation)
    {
        if (is_int($alternation)) {
            $this->maxAlternation = $alternation;
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return $this;
    }

    /**
     * Maximum number of lines allowed for the text labels.
     *
     * Labels can span multiple lines if they are too long,
     * and the nuber of lines is, by default, limited by
     * the height of the available space.
     *
     * This option is only supported for a discrete axis.
     *
     * @param  int $maxTextLines
     * @throws InvalidConfigValue
     * @return Axis
     */
    public function maxTextLines($maxTextLines)
    {
        if (is_int($maxTextLines)) {
            $this->maxTextLines = $maxTextLines;
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return $this;
    }

    /**
     * Minimum spacing, in pixels, allowed between two adjacent text labels.
     *
     * If the labels are spaced too densely, or they are too long,
     * the spacing can drop below this threshold, and in this case one of the
     * label-unclutter measures will be applied (e.g, truncating the lables or
     * dropping some of them).
     *
     * This option is only supported for a discrete axis.
     *
     * @param  int $minTextSpacing
     * @throws InvalidConfigValue
     * @return Axis
     */
    public function minTextSpacing($minTextSpacing)
    {
        if (is_int($minTextSpacing)) {
            $this->minTextSpacing = $minTextSpacing;
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return $this;
    }

    /**
     * Axis property that specifies the highest axis grid line. The
     * actual grid line will be the greater of two values: the maxValue option
     * value, or the highest data value, rounded up to the next higher grid mark.
     *
     * This option is only supported for a continuous axis.
     *
     * @param  int $max
     * @throws InvalidConfigValue
     * @return Axis
     */
    public function maxValue($max)
    {
        if (is_int($max)) {
            $this->maxValue = $max;
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
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
     * @param  int $min
     * @throws InvalidConfigValue
     * @return Axis
     */
    public function minValue($min)
    {
        if (is_int($min)) {
            $this->minValue = $min;
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return $this;
    }

    /**
     * How many axis labels to show
     *
     * 1 means show every label,
     * 2 means show every other label, and so on.
     * Default is to try to show as many labels as possible without overlapping.
     *
     * This option is only supported for a discrete axis.
     *
     * @param  int $showTextEvery
     * @throws InvalidConfigValue
     * @return Axis
     */
    public function showTextEvery($showTextEvery)
    {
        if (is_int($showTextEvery)) {
            $this->showTextEvery = $showTextEvery;
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return $this;
    }

    /**
     * Position of the axis text, relative to the chart area.
     * Supported values: 'out', 'in', 'none'.
     *
     * @param  string $position Setting the position of the text.
     * @throws InvalidConfigValue
     * @return Axis
     */
    public function textPosition($position)
    {
        $values = array(
            'out',
            'in',
            'none'
        );

        if (Utils::nonEmptyString($position) && in_array($position, $values)) {
            $this->textPosition = $position;
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string',
                'with a value of '.Utils::arrayToPipedString($values)
            );
        }

        return $this;
    }

    /**
     * Sets the textstyle for the axis
     *
     * @param  TextStyle $textStyle
     * @throws InvalidConfigValue
     * @return Axis
     */
    public function textStyle(TextStyle $textStyle)
    {
        $this->textStyle = $textStyle->getValues();

        return $this;
    }

    /**
     * Axis property that specifies the title of the axis.
     *
     * @param  string $title
     * @throws InvalidConfigValue
     * @return Axis
     */
    public function title($title)
    {
        if (Utils::nonEmptyString($title)) {
            $this->title = $title;
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }

    /**
     * An object that specifies the axis title text style.
     *
     * @param  TextStyle $titleTextStyle
     * @throws InvalidConfigValue
     * @return Axis
     */
    public function titleTextStyle(TextStyle $titleTextStyle)
    {
        $this->titleTextStyle = $titleTextStyle->getValues();

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
     * VerticalAxis->viewWindow['max'], it defines a half-opened range (min, max)
     * that denotes the element indices to display. In other words, every index
     * such that min <= index < max will be displayed.
     *
     * 'max' - The zero-based row index where the cropping window ends. Data
     * points at this index and higher will be cropped out. In conjunction with
     * VerticalAxis->viewWindow['min'], it defines a half-opened range (min, max)
     * that denotes the element i(ndices to display. In other words, every index
     * such that min <= index < max will be displayed.
     *
     * @param  array $viewWindow
     * @throws InvalidConfigValue
     * @return Axis
     */
    public function viewWindow($viewWindow)
    {
        if (is_array($viewWindow) &&
            array_key_exists('min', $viewWindow) &&
            array_key_exists('max', $viewWindow) &&
            is_int($viewWindow['min']) &&
            is_int($viewWindow['max'])
        ) {
            $this->viewWindow['viewWindowMin'] = $viewWindow['min'];
            $this->viewWindow['viewWindowMax'] = $viewWindow['max'];

            $this->viewWindowMode('explicit');
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'array',
                'with the structure min => (int), max => (int)'
            );
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
     * @param  string $viewMode
     * @throws InvalidConfigValue
     * @return Axis
     */
    public function viewWindowMode($viewMode)
    {
        $values = array(
            'pretty',
            'maximized',
            'explicit'
        );

        if (Utils::nonEmptyString($viewMode) && in_array($viewMode, $values)) {
            $this->viewWindowMode = $viewMode;
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string',
                'with a value of '.Utils::arrayToPipedString($values)
            );
        }

        return $this;
    }
}
