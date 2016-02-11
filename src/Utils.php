<?php

namespace Khill\Lavacharts;

class Utils
{
    /**
     * Checks if variable is a non-empty string
     *
     * @param  string $var
     * @return bool
     */
    public static function nonEmptyString($var)
    {
        if (is_string($var) && strlen($var) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks if variable is a non-empty string within an array of values
     *
     * @param  string $var
     * @param  array  $arr
     * @return bool
     */
    public static function nonEmptyStringInArray($var, $arr)
    {
        $stringCheck = (is_string($var) === true && strlen($var) > 0);
        $arrayCheck  = (is_array($arr) === true && in_array($var, $arr) === true);

        if ($stringCheck && $arrayCheck) {
            return true;
        } else {
            return false;
        }
    }
}
