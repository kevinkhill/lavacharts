<?php

namespace Khill\Lavacharts\DataTables\Formats;

use \Khill\Lavacharts\Options;

/**
 * NumberFormat Object
 *
 * Formats number values in the datatable for display.
 * Added to columns during column definition.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Formats
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 * @see        https://developers.google.com/chart/interactive/docs/reference#numberformatter
 */
class NumberFormat extends Format
{
    /**
     * Type of format object
     *
     * @var string
     */
    const TYPE = 'NumberFormat';

    /**
     * Default options for NumberFormat
     *
     * @var array
     */
    private $defaults = [
        'decimalSymbol',
        'fractionDigits',
        'groupingSymbol',
        'negativeColor',
        'negativeParens',
        'pattern',
        'prefix',
        'suffix'
    ];

    /**
     * Builds the NumberFormat object with specified options
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
     * Sets the character to use as the decimal marker.
     * The default is a dot (.)
     *
     * @param  string $decimalSymbol
     * @return \Khill\Lavacharts\DataTables\Formats\NumberFormat
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function decimalSymbol($decimalSymbol)
    {
        return $this->setStringOption(__FUNCTION__, $decimalSymbol);
    }

    /**
     * A number specifying how many digits to display after the decimal.
     *
     *
     * The default is 2. If you specify more digits than the number contains,
     * it will display zeros for the smaller values.
     * Truncated values will be rounded (5 rounded up).
     *
     * @param  integer $fractionDigits
     * @return \Khill\Lavacharts\DataTables\Formats\NumberFormat
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function fractionDigits($fractionDigits)
    {
        return $this->setNumericOption(__FUNCTION__, $fractionDigits);
    }

    /**
     * A character to be used to group digits to the left of the decimal into sets of three.
     * Default is a comma (,)
     *
     * @param  string $groupingSymbol
     * @return \Khill\Lavacharts\DataTables\Formats\NumberFormat
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function groupingSymbol($groupingSymbol)
    {
        return $this->setStringOption(__FUNCTION__, $groupingSymbol);
    }

    /**
     * The text color for negative values.
     *
     * No default value & values can be any acceptable HTML color value,
     * such as "red" or "#FF0000".
     *
     * @param  string $negativeColor Valid HTML color
     * @return \Khill\Lavacharts\DataTables\Formats\NumberFormat
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function negativeColor($negativeColor)
    {
        return $this->setStringOption(__FUNCTION__, $negativeColor);
    }

    /**
     * Sets whether negative values should be surrounded by parentheses.
     *
     * Default is true.
     *
     * @param  boolean $negativeParens
     * @return \Khill\Lavacharts\DataTables\Formats\NumberFormat
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function negativeParens($negativeParens)
    {
        return $this->setBoolOption(__FUNCTION__, $negativeParens);
    }

    /**
     * A format string. When provided, all other options are ignored, except negativeColor.
     *
     * The format string is a subset of the ICU pattern set.
     * For instance, {pattern:'#,###%'} will result in output values
     * "1,000%", "750%", and "50%" for values 10, 7.5, and 0.5.
     *
     * @see    http://icu-project.org/apiref/icu4c/classDecimalFormat.html#_details
     * @param  string $pattern
     * @return \Khill\Lavacharts\DataTables\Formats\NumberFormat
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function pattern($pattern)
    {
        return $this->setStringOption(__FUNCTION__, $pattern);
    }

    /**
     * Sets the string prefix for the value.
     *
     * @param  string $prefix
     * @return \Khill\Lavacharts\DataTables\Formats\NumberFormat
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function prefix($prefix)
    {
        return $this->setStringOption(__FUNCTION__, $prefix);
    }

    /**
     * Sets the string suffix for the value.
     *
     * @param  string $suffix
     * @return \Khill\Lavacharts\DataTables\Formats\NumberFormat
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function suffix($suffix)
    {
        return $this->setStringOption(__FUNCTION__, $suffix);
    }
}
