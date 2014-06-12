<?php namespace Khill\Lavacharts\Configs;

/**
 * JsDate Object
 *
 * PHP wrapper class used to create a date object the same way the javascript
 * creates date objects.
 *
 *
 * @category  Class
 * @package   Khill\Lavacharts\Configs
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2014, KHill Designs
 * @link      https://github.com/kevinkhill/LavaCharts GitHub Repository Page
 * @link      http://kevinkhill.github.io/LavaCharts/ GitHub Project Page
 * @license   http://opensource.org/licenses/GPL-3.0 GPLv3
 */

use Khill\Lavacharts\Exceptions\InvalidConfigValue;

class JsDate
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
     *
     *
     * @param int Year
     * @param int Month (REMEMBER, javascript dates start with 0 = Jan, 1 = Feb, etc.)
     * @param int Day
     * @param int Hour
     * @param int Minute
     * @param int Second
     * @param int Millisecond
     *
     * @return Khill\Lavacharts\Configs\JsDate
     */
    public function __construct(
        $year = null,
        $month = null,
        $day = null,
        $hour = null,
        $minute = null,
        $second = null,
        $millisecond = null
    ) {
        $this->year        = $year;
        $this->month       = $month;
        $this->day         = $day;
        $this->hour        = $hour;
        $this->minute      = $minute;
        $this->second      = $second;
        $this->millisecond = $millisecond;

        return $this;
    }

    /**
     * Parses array of values corresponding constructor arguments
     * or
     * Parses a date string according to the format specified by the standard
     * php function date_parse()
     *
     * @param array
     *
     * @return Khill\Lavacharts\Configs\JsDate
     */
    public function parse($arrayOrString)
    {
        if (is_array($arrayOrString)) {
            $this->parseArray($arrayOrString);
        }

        if (is_string($arrayOrString)) {
            $this->parseString($arrayOrString);
        }

        return $this;
    }

    /**
     * Outputs the object as a valid javascript string.
     *
     * @return string Javscript date declaration
     */
    public function toString()
    {
        if ($this->hour !== null && is_int($this->hour)) {
            if ($this->minute !== null && is_int($this->minute)) {
                if ($this->second !== null && is_int($this->second)) {
                    if ($this->millisecond !== null && is_int($this->millisecond)) {
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

    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Parses array of values with the order corresponding to the constructor arguments
     *
     * @param array
     */
    private function parseArray($array)
    {
        $this->year        = isset($array[0]) ? (int) $array[0] : null;
        $this->month       = isset($array[1]) ? (int) $array[1] : null;
        $this->day         = isset($array[2]) ? (int) $array[2] : null;
        $this->hour        = isset($array[3]) ? (int) $array[3] : null;
        $this->minute      = isset($array[4]) ? (int) $array[4] : null;
        $this->second      = isset($array[5]) ? (int) $array[5] : null;
        $this->millisecond = isset($array[6]) ? (int) $array[6] : null;
    }

    /**
     * Parses a date string according to the format specified by the standard
     * php function date_parse()
     *
     * @param string
     */
    private function parseString($dateString)
    {
        $date = date_parse($dateString);

        $this->year        = isset($date['year'])     ? (int) $date['year']     : null;
        $this->month       = isset($date['month'])    ? (int) $date['month']    : null;
        $this->day         = isset($date['day'])      ? (int) $date['day']      : null;
        $this->hour        = isset($date['hour'])     ? (int) $date['hour']     : null;
        $this->minute      = isset($date['minute'])   ? (int) $date['minute']   : null;
        $this->second      = isset($date['second'])   ? (int) $date['second']   : null;
        $this->millisecond = isset($date['fraction']) ? intval($date['fraction'] * 1000) : null;
    }
}
