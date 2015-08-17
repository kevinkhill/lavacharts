<?php

namespace Khill\Lavacharts\DataTables\Formats;

/**
 * DateFormat Object
 *
 * Formats date values in the datatable for display.
 * Added to columns during column definition.
 *
 *
 * @package    Lavacharts
 * @subpackage Formats
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 * @see        https://developers.google.com/chart/interactive/docs/reference#dateformatter
 */

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

class DateFormat extends Format
{
    /**
     * Type of format object
     *
     * @var string
     */
    const TYPE = 'DateFormat';

    /**
     * A quick formatting option for the date.
     *
     * @var string
     */
    public $formatType;

    /**
     * Format string, as a subset of the ICU pattern set.
     *
     * @var string
     */
    public $pattern;

    /**
     * timeZone to assign to values in the visualization.
     *
     * @var string
     */
    public $timeZone;


    /**
     * Builds the NumberFormat object with specified options
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
     * Sets a quick formatting option for the date.
     *
     * The following string values are supported,
     * reformatting the date February 28, 2008 as shown:
     *
     * 'short'  - Short format: e.g., "2/28/08"
     * 'medium' - Medium format: e.g., "Feb 28, 2008"
     * 'long'   - Long format: e.g., "February 28, 2008"
     *
     * You cannot specify both formatType and pattern.
     *
     * @param  string $ft
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function formatType($ft)
    {
        $values = [
            'short',
            'medium',
            'long'
        ];

        if (Utils::nonEmptyStringInArray($ft, $values)) {
            $this->formatType = $ft;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }

    /**
     * A custom format pattern to apply to the value, similar to the ICU date and time format.
     * For example: new DateFormat({pattern: "EEE, MMM d, ''yy"});
     *
     * You cannot specify both formatType and pattern.
     *
     * @see    http://userguide.icu-project.org/formatparse/datetime#TOC-Date-Field-Symbol-Table
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
     * @param  string $tz
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function timeZone($tz)
    {
        if (Utils::nonEmptyString($tz)) {
            $this->timeZone = $tz;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }
}
