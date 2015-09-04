<?php

namespace Khill\Lavacharts\DataTables\Formats;

use \Khill\Lavacharts\Options;

/**
 * BarFormat Object
 *
 * Adds a colored bar to a numeric cell indicating whether the cell value
 * is above or below a specified base value.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Formats
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 * @see        https://developers.google.com/chart/interactive/docs/reference#barformatter
 */
class BarFormat extends Format
{
    /**
     * Type of format object
     *
     * @var string
     */
    const TYPE = 'BarFormat';

    /**
     * Default options for BarFormat
     *
     * @var array
     */
    private $defaults = [
        'base',
        'colorNegative',
        'colorPositive',
        'drawZeroLine',
        'max',
        'min',
        'showValue',
        'width'
    ];

    /**
     * Builds the BarFormat object with specified options
     *
     * @param  array $config
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function __construct($config = [])
    {
        $options = new Options($this->defaults);

        parent::__construct($options, $config);
    }

    /**
     * A number indicating the base value, used to compare against the cell value.
     *
     * If the cell value is higher, the cell will include a green up arrow.
     * If the cell value is lower, it will include a red down arrow.
     * If the same, no arrow.
     *
     * @param  int|float $base
     * @return \Khill\Lavacharts\DataTables\Formats\BarFormat
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function base($base)
    {
        return $this->setNumericOption(__FUNCTION__, $base);
    }

    /**
     * A string indicating the negative value section of bars.
     *
     * Possible values are 'red', 'green' and 'blue';
     *
     * @access public
     * @param  string $colorNegative
     * @return \Khill\Lavacharts\DataTables\Formats\BarFormat
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function colorNegative($colorNegative)
    {
        $values = [
            'red',
            'green',
            'blue'
        ];

        return $this->setStringInArrayOption(__FUNCTION__, $colorNegative, $values);
    }

    /**
     * A string indicating the color of the positive value section of bars.
     *
     * Possible values are 'red', 'green' and 'blue'. Default is 'blue'.
     *
     * @access public
     * @param  string $colorPositive
     * @return \Khill\Lavacharts\DataTables\Formats\BarFormat
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function colorPositive($colorPositive)
    {
        $values = [
            'red',
            'green',
            'blue'
        ];

        return $this->setStringInArrayOption(__FUNCTION__, $colorPositive, $values);
    }

    /**
     * A boolean indicating if to draw a 1 pixel dark base line when negative values are present.
     *
     * The dark line is there to enhance visual scanning of the bars.
     *
     * @access public
     * @param  boolean $drawZeroLine
     * @return \Khill\Lavacharts\DataTables\Formats\BarFormat
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function drawZeroLine($drawZeroLine)
    {
        return $this->setBoolOption(__FUNCTION__, $drawZeroLine);
    }

    /**
     * The maximum number value for the bar range.
     *
     * Default value is the highest value in the table.
     *
     * @access public
     * @param  int|float $max
     * @return \Khill\Lavacharts\DataTables\Formats\BarFormat
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function max($max)
    {
        return $this->setNumericOption(__FUNCTION__, $max);
    }

    /**
     * The minimum number value for the bar range.
     *
     * Default value is the lowest value in the table.
     *
     * @access public
     * @param  int|float $min
     * @return \Khill\Lavacharts\DataTables\Formats\BarFormat
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function min($min)
    {
        return $this->setNumericOption(__FUNCTION__, $min);
    }

    /**
     * If true, shows values and bars; if false, shows only bars.
     *
     * @access public
     * @param  boolean $drawZeroLine
     * @return \Khill\Lavacharts\DataTables\Formats\BarFormat
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function showValue($showValue)
    {
        return $this->setBoolOption(__FUNCTION__, $showValue);
    }

    /**
     * Thickness of each bar, in pixels.
     *
     * @access public
     * @param  boolean $width
     * @return \Khill\Lavacharts\DataTables\Formats\BarFormat
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function width($width)
    {
        $this->setIntOption(__FUNCTION__, $width);
    }
}
