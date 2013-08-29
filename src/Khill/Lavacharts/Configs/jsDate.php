<?php namespace Khill\Lavacharts\Configs;
/**
 * jsDate Object
 *
 * PHP wrapper class used to create a date object the same way the javascript
 * creates date objects.
 *
 *
 * @author Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2013, KHill Designs
 * @link https://github.com/kevinkhill/Codeigniter-gCharts GitHub Repository Page
 * @link http://kevinkhill.github.io/Codeigniter-gCharts/ GitHub Project Page
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
     * Designed to work the same way the javascript date object works.
     *
     * @param int Year
     * @param int Month (starting with 0 = Jan, 1 = Feb, etc.)
     * @param int Day
     * @param int Hour
     * @param int Minute
     * @param int Second
     * @param int Millisecond
     * @return \jsDate
     */
    public function __construct(
        $year = NULL,
        $month = NULL,
        $day = NULL,
        $hour = NULL,
        $minute = NULL,
        $second = NULL,
        $millisecond = NULL
    )
    {
        $this->year = $year;
        $this->month = $month;
        $this->day = $day;
        $this->hour = $hour;
        $this->minute = $minute;
        $this->second = $second;
        $this->millisecond = $millisecond;

        return $this;
    }

    /**
     * Parses array of values corresponding to if called as args to constructor
     *
     * @param array
     * @return \jsDate
     */
    public function parseArray($array)
    {
        $this->year        = isset($array[0]) ? $array[0] : NULL;
        $this->month       = isset($array[1]) ? $array[1] : NULL;
        $this->day         = isset($array[2]) ? $array[2] : NULL;
        $this->hour        = isset($array[3]) ? $array[3] : NULL;
        $this->minute      = isset($array[4]) ? $array[4] : NULL;
        $this->second      = isset($array[5]) ? $array[5] : NULL;
        $this->millisecond = isset($array[6]) ? $array[6] : NULL;

        return $this;
    }

    /**
     * Outputs the object as a valide javascript string.
     *
     * @return string Javscript date declaration
     */
    public function toString()
    {
        if($this->hour !== NULL && is_int($this->hour))
        {
            if($this->minute !== NULL && is_int($this->minute))
            {
                if($this->second !== NULL && is_int($this->second))
                {
                    if($this->millisecond !== NULL && is_int($this->millisecond))
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
