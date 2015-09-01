<?php

namespace Khill\Lavacharts\DataTables\Formats;

/**
 * DateFormat Object
 *
 * Formats date values in the datatable for display.
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
 * @see        https://developers.google.com/chart/interactive/docs/reference#dateformatter
 */

use \Khill\Lavacharts\Options;

class DateFormat extends Format
{
    /**
     * Type of format object
     *
     * @var string
     */
    const TYPE = 'DateFormat';

    /**
     * Default options for DateFormat
     *
     * @var array
     */
    private $defaults = [
        'formatType',
        'pattern',
        'timeZone'
    ];

    /**
     * Builds the DateFormat object with specified options
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
     * Sets a quick formatting option for the date.
     * The following string values are supported,
     * reformatting the date February 28, 2008 as shown:
     * 'short'  - Short format: e.g., "2/28/08"
     * 'medium' - Medium format: e.g., "Feb 28, 2008"
     * 'long'   - Long format: e.g., "February 28, 2008"
     * You cannot specify both formatType and pattern.
     *
     * @param  string $formatType
     * @return \Khill\Lavacharts\DataTables\Formats\DateFormat
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function formatType($formatType)
    {
        $values = [
            'short',
            'medium',
            'long'
        ];

        return $this->setStringInArrayOption(__FUNCTION__, $formatType, $values);
    }

    /**
     * A custom format pattern to apply to the value, similar to the ICU date and time format.
     *
     * For example: "EEE, MMM d, 'yy"
     * Also, you cannot specify both formatType and pattern.
     *
     *
     * @see    http://userguide.icu-project.org/formatparse/datetime#TOC-Date-Field-Symbol-Table
     * @param  string $pattern
     * @return \Khill\Lavacharts\DataTables\Formats\DateFormat
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function pattern($pattern)
    {
        return $this->setStringOption(__FUNCTION__, $pattern);
    }

    /**
     * Sets the time zone in which to display the date value.
     *
     * This is a numeric value, indicating GMT + this number of time zones (can be negative).
     * Date object are created by default with the assumed time zone of the computer on which they are created;
     * this option is used to display that value in a different time zone.
     *
     * For example, if you created a Date object of 5pm noon on a computer located in Greenwich, England,
     * and specified timeZone to be -5 (options['timeZone'] = -5, or Eastern Pacific Time in the US),
     * the value displayed would be 12 noon.
     *
     *
     * @param  string $timeZone
     * @return \Khill\Lavacharts\DataTables\Formats\DateFormat
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function timeZone($timeZone)
    {
        return $this->setStringOption(__FUNCTION__, $timeZone);
    }
}
