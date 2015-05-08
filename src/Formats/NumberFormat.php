<?php

namespace Khill\Lavacharts\Formats;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * NumberFormat Object
 *
 * Formats number values in the datatable for display.
 * Added to columns during column definition.
 *
 * @see        https://developers.google.com/chart/interactive/docs/reference#numberformatter
 *
 * @package    Lavacharts
 * @subpackage Formats
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class NumberFormat extends Format
{
    const TYPE = 'NumberFormat';

    /**
     * Character to use as the decimal marker.
     *
     * @var string
     */
    public $decimalSymbol;

    /**
     * How many digits to display after the decimal.
     *
     * @var int
     */
    public $fractionDigits;

    /**
     * Character to be used to group digits.
     *
     * @var string
     */
    public $groupingSymbol;

    /**
     * Text color for negative values.
     *
     * @var string
     */
    public $negativeColor;

    /**
     * Indicates that negative values should be surrounded by parentheses
     *
     * @var boolean
     */
    public $negativeParens;

    /**
     * Format string, as a subset of the ICU pattern set.
     *
     * @var string
     */
    public $pattern;

    /**
     * prefix to assign to values in the visualization.
     *
     * @var string
     */
    public $prefix;

    /**
     * suffix to assign to values in the visualization.
     *
     * @var string
     */
    public $suffix;

    /**
     * Builds the NumberFormat object with specified options
     *
     * @param  array                 $config
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     * @return self
     */
    public function __construct($config = array())
    {
        parent::__construct($this, $config);
    }

    /**
     * Sets the character to use as the decimal marker.
     *
     * The default is a dot (.)
     *
     * @param  string $ds
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function decimalSymbol($ds)
    {
        if (Utils::nonEmptyString($ds)) {
            $this->decimalSymbol = $ds;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }

    /**
     * A number specifying how many digits to display after the decimal.
     *
     *
     * The default is 2. If you specify more digits than the number contains,
     * it will display zeros for the smaller values.
     * Truncated values will be rounded (5 rounded up).
     *
     * @param  integer $fd
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function fractionDigits($fd)
    {
        if (is_numeric($fd)) {
            $this->fractionDigits = (int) $fd;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'numeric'
            );
        }

        return $this;
    }

    /**
     * A character to be used to group digits to the left of the decimal into sets of three.
     * Default is a comma (,)
     *
     * @param  string $gs
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function groupingSymbol($gs)
    {
        if (Utils::nonEmptyString($gs)) {
            $this->groupingSymbol = $gs;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }

    /**
     * The text color for negative values.
     *
     * No default value & values can be any acceptable HTML color value,
     * such as "red" or "#FF0000".
     *
     * @param  string $nc Valid HTML color
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function negativeColor($nc)
    {
        if (Utils::nonEmptyString($nc)) {
            $this->negativeColor = $nc;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }

    /**
     * Sets whether negative values should be surrounded by parentheses.
     *
     * Default is true.
     *
     * @param  boolean $np
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function negativeParens($np)
    {
        if (is_bool($np)) {
            $this->negativeParens = $np;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'bool'
            );
        }

        return $this;
    }

    /**
     * A format string. When provided, all other options are ignored, except negativeColor.
     *
     * The format string is a subset of the ICU pattern set.
     * For instance, {pattern:'#,###%'} will result in output values
     * "1,000%", "750%", and "50%" for values 10, 7.5, and 0.5.
     *
     * @see    http://icu-project.org/apiref/icu4c/classDecimalFormat.html#_details
     * @param  string $p
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function pattern($p)
    {
        if (Utils::nonEmptyString($p)) {
            $this->pattern = $p;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }

    /**
     * Sets the string prefix for the value.
     *
     * @param  string $p
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function prefix($p)
    {
        if (Utils::nonEmptyString($p)) {
            $this->prefix = $p;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }

    /**
     * Sets the string suffix for the value.
     *
     * @param  string $s
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function suffix($s)
    {
        if (Utils::nonEmptyString($s)) {
            $this->suffix = $s;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }
}
