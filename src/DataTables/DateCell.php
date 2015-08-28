<?php

namespace Khill\Lavacharts\DataTables;

use \Carbon\Carbon;
use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Exceptions\FailedCarbonParsing;
use \Khill\Lavacharts\Exceptions\InvalidDateTimeString;

/**
 * DateCell Class
 *
 * Wrapper object to implement JsonSerializable on the Carbon object.
 *
 * @package    Khill\Lavacharts
 * @subpackage DataTables
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class DateCell implements \JsonSerializable
{
    /**
     * Carbon object of the date value in the DataTable row.
     *
     * @var \Carbon\Carbon
     */
    private $date;

    /**
     * Creates a new DateCell object from a Carbon object.
     *
     * @param  Carbon $carbon
     * @return self
     */
    public function __construct(Carbon $carbon)
    {
        $this->date = $carbon;
    }

    /**
     * @param  string $dateTimeString
     * @param  string $dateTimeFormat
     * @return \Khill\Lavacharts\DataTables\DateCell
     * @throws \Khill\Lavacharts\Exceptions\FailedCarbonParsing
     * @throws \Khill\Lavacharts\Exceptions\InvalidDateTimeString
     */
    public static function parseString($dateTimeString, $dateTimeFormat)
    {
        if (Utils::nonEmptyString($dateTimeString) === false) {
            throw new InvalidDateTimeString($dateTimeString);
        }

        try {
            if (Utils::nonEmptyString($dateTimeFormat) === true) {
                $carbon = Carbon::createFromFormat($dateTimeFormat, $dateTimeString);
            } else {
                $carbon = Carbon::parse($dateTimeString);
            }
        } catch (\Exception $e) {
            throw new FailedCarbonParsing($dateTimeString);
        }

        return new DateCell($carbon);
    }

    /**
     * Custom string output of the Carbon date.
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            'Date(%d,%d,%d,%d,%d,%d)',
            isset($this->date->year)   ? $this->date->year      : 'null',
            isset($this->date->month)  ? $this->date->month - 1 : 'null', //silly javascript
            isset($this->date->day)    ? $this->date->day       : 'null',
            isset($this->date->hour)   ? $this->date->hour      : 'null',
            isset($this->date->minute) ? $this->date->minute    : 'null',
            isset($this->date->second) ? $this->date->second    : 'null'
        );
    }

    /**
     * Custom serialization of the Carbon date.
     *
     * @return string
     */
    public function jsonSerialize()
    {
        return (string) $this;
    }
}
