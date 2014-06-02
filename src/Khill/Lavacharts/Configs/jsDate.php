<?php namespace Khill\Lavacharts\Configs;
/**
 * jsDate Object
 *
 * PHP wrapper class used to create a date object the same way the javascript
 * creates date objects.
 *
 *
 * @author Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2014, KHill Designs
 * @link https://github.com/kevinkhill/LavaCharts GitHub Repository Page
 * @link http://kevinkhill.github.io/LavaCharts/ GitHub Project Page
 * @license http://opensource.org/licenses/MIT MIT
 */

class jsDate
{
    /**
     * Holds the output of the jsDate object.
     *
     * @var string
     */
    public $output;

    /**
     * Builds the jsDate object.
     *
     * Designed to work the same way the exactly like the javascript date object works.
     * with some extra functionality.
     *
     * Pass in an array of corresponding values to be parsed
     * or
     * Pass in a string to be parsed by the php function parse_date()
     *
     * @param int|string|array Year
     * @param int Month (starting with 0 = Jan, 1 = Feb, etc.)
     * @param int Day
     * @param int Hour
     * @param int Minute
     * @param int Second
     * @param int Millisecond
     * @return \jsDate
     */
    public function __construct(
        $year        = null,
        $month       = null,
        $day         = null,
        $hour        = null,
        $minute      = null,
        $second      = null,
        $millisecond = null
    ) {
        if ( is_string($year) ) {
            return $this->parseString($year);
        } else {
            $this->year        = $year;
            $this->month       = $month;
            $this->day         = $day;
            $this->hour        = $hour;
            $this->minute      = $minute;
            $this->second      = $second;
            $this->millisecond = $millisecond;

            return $this;
        }
    }


    /**
     * Parses a date string according to the format specified by the standard
     * php function date_parse()
     *
     * @param string
     * @return \jsDate
     */
    private function parseString($dateString)
    {
        $date = date_parse($dateString);

        $this->year        = isset($date['year'])     ? $date['year']     : null;
        $this->month       = isset($date['month'])    ? $date['month']    : null;
        $this->day         = isset($date['day'])      ? $date['day']      : null;
        $this->hour        = isset($date['hour'])     ? $date['hour']     : null;
        $this->minute      = isset($date['minute'])   ? $date['minute']   : null;
        $this->second      = isset($date['second'])   ? $date['second']   : null;
        $this->millisecond = isset($date['fraction']) ? $date['fraction'] : null;

        return $this;
    }

    /**
     * Outputs the object as a valid javascript string.
     *
     * @return string Javscript date declaration
     */
    public function toString()
    {
        if($this->hour !== null && is_int($this->hour))
        {
            if($this->minute !== null && is_int($this->minute))
            {
                if($this->second !== null && is_int($this->second))
                {
                    if($this->millisecond !== null && is_int($this->millisecond))
                    {
                        $this->format = 'Date(%d, %d, %d, %d, %d, %d, %d)';
                        $this->output = sprintf($this->format, $this->year, $this->month, $this->day, $this->hour, $this->minute, $this->second, $this->millisecond);
                    } else {
                        $this->format = 'Date(%d, %d, %d, %d, %d, %d)';
                        $this->output = sprintf($this->format, $this->year, $this->month, $this->day, $this->hour, $this->minute, $this->second);
                    }
                } else {
                    $this->format = 'Date(%d, %d, %d, %d, %d)';
                    $this->output = sprintf($this->format, $this->year, $this->month, $this->day, $this->hour, $this->minute);
                }
            } else {
                $this->format = 'Date(%d, %d, %d, %d)';
                $this->output = sprintf($this->format, $this->year, $this->month, $this->day, $this->hour);
            }
        } else {
            $this->format = 'Date(%d, %d, %d)';
            $this->output = sprintf($this->format, $this->year, $this->month, $this->day);
        }

        return $this->output;
    }
}
