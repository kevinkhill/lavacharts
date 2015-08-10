<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

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
class Axis extends JsonConfig
{
    /**
     * Type of JsonConfig object
     *
     * @var string
     */
    const TYPE = 'Axis';

    /**
     * Default options for Axis
     *
     * @var array
     */
    private $defaults = [
        'baseline',
        'baselineColor',
        'direction',
        'format',
        'gridlines',
        'logScale',
        'maxAlternation',
        'maxTextLines',
        'maxValue',
        'minorGridlines',
        'minTextSpacing',
        'minValue',
        'showTextEvery',
        'textPosition',
        'textStyle',
        'title',
        'titleTextStyle',
        'viewWindow',
        'viewWindowMode'
    ];

    /**
     * Builds the configuration when passed an array of options.
     *
     * All options can be set by either passing an array with associative
     * values for option => value, or by chaining together the functions once
     * an object has been created.
     *
     * @param  array $config Associative array containing key => value pairs for the various configuration options.
     * @throws InvalidConfigValue
     * @return \Khill\Lavacharts\Configs\Axis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function __construct($config = [])
    {
        $options = new Options($this->defaults);

        parent::__construct($options, $config);
    }

    /**
     * Sets the baseline for the axis.
     *
     * This option is only supported for a continuous axis.
     *
     * @param  mixed $baseline Must match type defined for the column, [ number | Carbon ].
     * @return \Khill\Lavacharts\Configs\Axis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function baseline($baseline) //@TODO convert to Carbon
    {
        if (Utils::isJsDate($baseline)) {
            $this->baseline = $baseline->toString();
        } else {
            if (is_int($baseline)) {
                $this->baseline = $baseline;
            } else {
                throw new InvalidConfigValue(
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
     * @param  string $color Valid HTML color.
     * @return \Khill\Lavacharts\Configs\Axis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function baselineColor($color)
    {
        return $this->setStringOption(__FUNCTION__, $color);
    }

    /**
     * Sets the direction of the axis values.
     *
     * specify 1 for normal, -1 to reverse the order of the values.
     *
     * @param  int $direction
     * @return \Khill\Lavacharts\Configs\Axis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function direction($direction)
    {
        if (is_int($direction) === false || ($direction != 1 && $direction != -1)) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'int',
                '1 or -1'
            );
        }

        return $this->setOption(__FUNCTION__, $direction);
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
     * @return \Khill\Lavacharts\Configs\Axis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function format($format)
    {
        return $this->setStringOption(__FUNCTION__, $format);
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
     * @return \Khill\Lavacharts\Configs\Axis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function gridlines($gridlines) //TODO: Fix to use gridlines object
    {
        if (is_array($gridlines) && array_key_exists('count', $gridlines) && array_key_exists('color', $gridlines)) {
            if (Utils::nonEmptyString($gridlines['color'])) {
                $this->gridlines['color'] = $gridlines['color'];
            } else {
                throw new InvalidConfigValue(
                    __FUNCTION__,
                    'array',
                    'with the value of the key "color" being a valid HTML color'
                );
            }

            if (is_int($gridlines['count']) && $gridlines['count'] >= 2 || $gridlines['count'] == -1) {
                $this->gridlines['count'] = $gridlines['count'];
            } else {
                throw new InvalidConfigValue(
                    __FUNCTION__,
                    'array',
                    'with the value of the key "count" == -1 || >= 2'
                );
            }
        } else {
            throw new InvalidConfigValue(
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
     * @return \Khill\Lavacharts\Configs\Axis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function logScale($logScale)
    {
        return $this->setBoolOption(__FUNCTION__, $logScale);
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
     * @return \Khill\Lavacharts\Configs\Axis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function minorGridlines($minorGridlines) //TODO: use gridline object
    {
        if (is_array($minorGridlines) && array_key_exists('count', $minorGridlines) && array_key_exists('color', $minorGridlines)) {
            if (Utils::nonEmptyString($minorGridlines['color'])) {
                $this->minorGridlines['color'] = $minorGridlines['color'];
            } else {
                throw new InvalidConfigValue(
                    __FUNCTION__,
                    'array',
                    'with the value of the key "color" being a valid HTML color'
                );
            }

            if (is_int($minorGridlines['count']) && $minorGridlines['count'] >= 2 || $minorGridlines['count'] == -1) {
                $this->minorGridlines['count'] = $minorGridlines['count'];
            } else {
                throw new InvalidConfigValue(
                    __FUNCTION__,
                    'array',
                    'with the value of the key "count" == -1 || >= 2'
                );
            }
        } else {
            throw new InvalidConfigValue(
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
     * @return \Khill\Lavacharts\Configs\Axis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function maxAlternation($alternation)
    {
        return $this->setIntOption(__FUNCTION__, $alternation);
    }

    /**
     * Maximum number of lines allowed for the text labels.
     *
     * Labels can span multiple lines if they are too long,
     * and the number of lines is, by default, limited by
     * the height of the available space.
     *
     * This option is only supported for a discrete axis.
     *
     * @param  int $maxTextLines
     * @return \Khill\Lavacharts\Configs\Axis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function maxTextLines($maxTextLines)
    {
        return $this->setIntOption(__FUNCTION__, $maxTextLines);
    }

    /**
     * Minimum spacing, in pixels, allowed between two adjacent text labels.
     *
     * If the labels are spaced too densely, or they are too long,
     * the spacing can drop below this threshold, and in this case one of the
     * label-unclutter measures will be applied (e.g, truncating the labels or
     * dropping some of them).
     *
     * This option is only supported for a discrete axis.
     *
     * @param  int $minTextSpacing
     * @return \Khill\Lavacharts\Configs\Axis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function minTextSpacing($minTextSpacing)
    {
        return $this->setIntOption(__FUNCTION__, $minTextSpacing);
    }

    /**
     * Axis property that specifies the highest axis grid line. The
     * actual grid line will be the greater of two values: the maxValue option
     * value, or the highest data value, rounded up to the next higher grid mark.
     *
     * This option is only supported for a continuous axis.
     *
     * @param  int $max
     * @return \Khill\Lavacharts\Configs\Axis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function maxValue($max)
    {
        return $this->setIntOption(__FUNCTION__, $max);
    }

    /**
     * axis property that specifies the lowest axis grid line. The
     * actual grid line will be the lower of two values: the minValue option
     * value, or the lowest data value, rounded down to the next lower grid mark.
     *
     * This option is only supported for a continuous axis.
     *
     * @param  int $min
     * @return \Khill\Lavacharts\Configs\Axis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function minValue($min)
    {
        return $this->setIntOption(__FUNCTION__, $min);
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
     * @return \Khill\Lavacharts\Configs\Axis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function showTextEvery($showTextEvery)
    {
        return $this->setIntOption(__FUNCTION__, $showTextEvery);
    }

    /**
     * Position of the axis text, relative to the chart area.
     *
     * Supported values:
     * 'out', 'in', 'none'.
     *
     * @param  string $position Setting the position of the text.
     * @return \Khill\Lavacharts\Configs\Axis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function textPosition($position)
    {
        $values = [
            'out',
            'in',
            'none'
        ];

        return $this->setStringInArrayOption(__FUNCTION__, $position, $values);
    }

    /**
     * Sets the TextStyle options for the axis
     *
     * @param  array $textStyleConfig
     * @return \Khill\Lavacharts\Configs\Axis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function textStyle($textStyleConfig)
    {
        return $this->setOption(__FUNCTION__, new TextStyle($textStyleConfig));
    }

    /**
     * Axis property that specifies the title of the axis.
     *
     * @param  string $title
     * @return \Khill\Lavacharts\Configs\Axis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function title($title)
    {
        return $this->setStringOption(__FUNCTION__, $title);
    }

    /**
     * An object that specifies the axis title text style.
     *
     * @param  array $titleTextStyleConfig
     * @return \Khill\Lavacharts\Configs\Axis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function titleTextStyle($titleTextStyleConfig)
    {
        return $this->setOption(__FUNCTION__, new TextStyle($titleTextStyleConfig));
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
     * that denotes the element indices to display. In other words, every index
     * such that min <= index < max will be displayed.
     *
     * @param  array $viewWindow
     * @return \Khill\Lavacharts\Configs\Axis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function viewWindow($viewWindow) //TODO: fix the logic in this
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
            throw new InvalidConfigValue(
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
     * @return \Khill\Lavacharts\Configs\Axis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function viewWindowMode($viewMode)
    {
        $values = [
            'pretty',
            'maximized',
            'explicit'
        ];

        return $this->setStringInArrayOption(__FUNCTION__, $viewMode, $values);
    }
}
