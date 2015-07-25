<?php

namespace Khill\Lavacharts\DataTables\Formats;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * BarFormat Object
 *
 * Adds a colored bar to a numeric cell indicating whether the cell value
 * is above or below a specified base value.
 *
 *
 * @package    Lavacharts
 * @subpackage Formats
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 * @see        https://developers.google.com/chart/interactive/docs/reference#dateformatter
 */
class BarFormat extends Format
{
    const TYPE = 'BarFormat';

    /**
     * A number indicating the base value.
     *
     * @var int|float
     */
    public $base;

    /**
     * Negative value color.
     *
     * @var int|float
     */
    public $colorNegative;

    /**
     * Positive value color.
     *
     * @var int|float
     */
    public $colorPositive;

    /**
     * Draw dark base line when negative values are present.
     *
     * @var boolean
     */
    public $drawZeroLine;

    /**
     * Maximum number value.
     *
     * @var int|float
     */
    public $max;

    /**
     * Minimum number value.
     *
     * @var int|float
     */
    public $min;

    /**
     * Show value or only bars.
     *
     * @var boolean
     */
    public $showValue;

    /**
     * Thickness of bar.
     *
     * @var int
     */
    public $width;

    /**
     * Builds the BarFormat object with specified options
     *
     * @param  array $config
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     * @return self
     */
    public function __construct($config = [])
    {
        parent::__construct($this, $config);
    }

    /**
     * A number indicating the base value, used to compare against the cell value.
     *
     * If the cell value is higher, the cell will include a green up arrow.
     * If the cell value is lower, it will include a red down arrow.
     * If the same, no arrow.
     *
     * @param  int|float $base
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function base($base)
    {
        if (is_numeric($base) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'numeric'
            );
        }

        $this->base = $base;

        return $this;
    }

    /**
     * A string indicating the negative value section of bars.
     *
     * Possible values are 'red', 'green' and 'blue';
     *
     * @access public
     * @param  string $colorNegative
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function colorNegative($colorNegative)
    {
        $values = [
            'red',
            'green',
            'blue'
        ];

        if (in_array($shape, $values, true) === false) {
            throw new InvalidConfigValue(
                self::TYPE,
                __FUNCTION__,
                'string',
                'Possible values are '.Utils::arrayToPipedString($values)
            );
        }

        $this->colorNegative = $colorNegative;

        return $this;
    }

    /**
     * A string indicating the color of the positive value section of bars.
     *
     * Possible values are 'red', 'green' and 'blue'. Default is 'blue'.
     *
     * @access public
     * @param  string $colorPositive
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function colorPositive($colorPositive)
    {
        $values = [
            'red',
            'green',
            'blue'
        ];

        if (in_array($shape, $values, true) === false) {
            throw new InvalidConfigValue(
                self::TYPE,
                __FUNCTION__,
                'string',
                'Possible values are '.Utils::arrayToPipedString($values)
            );
        }

        $this->colorPositive = $colorPositive;

        return $this;
    }

    /**
     * A boolean indicating if to draw a 1 pixel dark base line when negative values are present.
     *
     * The dark line is there to enhance visual scanning of the bars.
     *
     * @access public
     * @param  boolean $drawZeroLine
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function drawZeroLine($drawZeroLine)
    {
        if (is_bool($drawZeroLine) === false) {
            throw new InvalidConfigValue(
                self::TYPE,
                __FUNCTION__,
                'boolean'
            );
        }

        $this->drawZeroLine = $drawZeroLine;

        return $this;
    }

    /**
     * The maximum number value for the bar range.
     *
     * Default value is the highest value in the table.
     *
     * @access public
     * @param  int|float $max
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function max($max)
    {
        if (is_numeric($max) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'numeric'
            );
        }

        $this->max = $max;

        return $this;
    }

    /**
     * The minimum number value for the bar range.
     *
     * Default value is the lowest value in the table.
     *
     * @access public
     * @param  int|float $min
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function min($min)
    {
        if (is_numeric($min) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'numeric'
            );
        }

        $this->min = $min;

        return $this;
    }

    /**
     * If true, shows values and bars; if false, shows only bars.
     *
     * @access public
     * @param  boolean $drawZeroLine
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function showValue($showValue)
    {
        if (is_bool($showValue) === false) {
            throw new InvalidConfigValue(
                self::TYPE,
                __FUNCTION__,
                'boolean'
            );
        }

        $this->showValue = $showValue;

        return $this;
    }

    /**
     * Thickness of each bar, in pixels.
     *
     * @access public
     * @param  boolean $width
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function width($width)
    {
        if (is_int($width) === false) {
            throw new InvalidConfigValue(
                self::TYPE,
                __FUNCTION__,
                'integer'
            );
        }

        $this->width = $width;

        return $this;
    }

}
