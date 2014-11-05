<?php namespace Khill\Lavacharts\Formats;

/**
 * DateFormat Object
 *
 * Formats date values in the datatable for display.
 * Added to columns during column definition.
 *
 * @see        https://developers.google.com/chart/interactive/docs/reference#dateformatter
 *
 * @package    Lavacharts
 * @subpackage Formatters
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2014, KHill Designs
 * @link       http://github.com/kevinkhill/Lavacharts GitHub Repository Page
 * @link       http://kevinkhill.github.io/Lavacharts  GitHub Project Page
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Helpers\Helpers as h;
use Khill\Lavacharts\Exceptions\InvalidConfigValue;

class DateFormat extends Format
{
    const TYPE = 'DateFormat';

    /**
     * A quick formatting option for the date.
     *
     * @var int
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
     * @param  array                 $config
     * @throws InvalidConfigValue
     * @throws InvalidConfigProperty
     * @return DateFormat
     */
    public function __construct($config = array())
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
     * @param  string             $formatType
     * @throws InvalidConfigValue
     * @return DateFormat
     */
    public function formatType($formatType)
    {
        $values = array(
            'short',
            'medium',
            'long'
        );

        if (is_string($formatType) && in_array($formatType, $values)) {
            $this->formatType = $formatType;
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
     * @param  string              $pattern
     * @throws InvalidConfigValue
     * @return DateFormat
     */
    public function pattern($pattern)
    {
        if (h::nonEmptyString($pattern)) {
            $this->pattern = $pattern;
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
     * @param  array              $timeZone
     * @throws InvalidConfigValue
     * @return DateFormat
     */
    public function timeZone($timeZone)
    {
        if (h::nonEmptyString($timeZone)) {
            $this->timeZone = $timeZone;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }

}
