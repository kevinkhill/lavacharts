<?php

namespace Khill\Lavacharts\Javascript;

use Carbon\Carbon;

/**
 * JavascriptDate Class
 *
 * This was made so that I could copy and paste Google's examples for my tests.
 *
 * @package    Khill\Lavacharts\Support
 * @since      4.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2017, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class JavascriptDate extends Carbon
{
    /**
     * Format string for sprintf output
     */
    const OUTPUT_FORMAT = '%s-%s-%s %s:%02s:%02s';

    /**
     * JavascriptDate constructor.
     */
    public function __construct()
    {
        $tz   = null;
        $hour = null;
        $args = func_get_args();

        if (isset($args[6])) {
            $tz = $args[6];
        }

        $defaults = array_combine([
            'year',
            'month',
            'day',
            'hour',
            'minute',
            'second',
        ], explode('-', date('Y-n-j-G-i-s', time())));

        $year  = isset($args[0]) ? $args[0]    : $defaults['year'];
        $month = isset($args[1]) ? $args[1] +1 : $defaults['month'];
        $day   = isset($args[2]) ? $args[2]    : $defaults['day'];

        if (isset($args[3]) === false) {
            $hour   = $defaults['hour'];
            $minute = isset($args[4]) ? $args[4] : $defaults['minute'];
            $second = isset($args[5]) ? $args[5] : $defaults['second'];
        } else {
            $minute = isset($args[4]) ? $args[4] : 0;
            $second = isset($args[5]) ? $args[5] : 0;
        }

        $format = '%s-%s-%s %s:%02s:%02s';

        return parent::__construct(sprintf(
            self::OUTPUT_FORMAT, $year, $month, $day, $hour, $minute, $second
        ), $tz);
    }

}
