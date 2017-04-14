<?php

namespace Khill\Lavacharts\Support;

use Carbon\Carbon;

class JavascriptDate extends Carbon
{
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

        $defaults = array_combine(array(
            'year',
            'month',
            'day',
            'hour',
            'minute',
            'second',
        ), explode('-', date('Y-n-j-G-i-s', time())));

        $year  = isset($args[0]) ? $args[0]    : $defaults['year'];
        $month = isset($args[1]) ? $args[1] +1 : $defaults['month'];
        $day   = isset($args[2]) ? $args[2]    : $defaults['day'];

        if (isset($args[3]) === false) {
            $hour = $defaults['hour'];
            $minute = isset($args[4]) ? $args[4] : $defaults['minute'];
            $second = isset($args[5]) ? $args[5] : $defaults['second'];
        } else {
            $minute = isset($args[4]) ? $args[4] : 0;
            $second = isset($args[5]) ? $args[5] : 0;
        }

        return parent::__construct(sprintf('%s-%s-%s %s:%02s:%02s', $year, $month, $day, $hour, $minute, $second), $tz);
    }

}
