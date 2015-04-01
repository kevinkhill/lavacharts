<?php namespace Khill\Lavacharts;

class Utils
{
    /**
     * Magic method as an alias to is_a($object, $type)
     *
     * @param string $function
     * @param object $configObject
     *
     * @return bool
     */
    public static function __callStatic($function, $configObject)
    {
        if (preg_match('/^is/', $function)) {
            $is_a = substr($function, 2);

            if (is_object($configObject[0])) {
                $class = new \ReflectionClass($configObject[0]);

                return preg_match("/{$is_a}/", $class->getShortname()) ? true : false;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Takes an array of values and ouputs them as a string between
     * brackets and separated by a pipe.
     *
     * @param array Array of default values
     *
     * @return string Converted array to string.
     */
    public static function arrayToPipedString($defaultValues)
    {
        if (is_array($defaultValues)) {
            $output = '[ ';

            natcasesort($defaultValues);

            foreach ($defaultValues as $value) {
                $output .= $value . ' | ';
            }

            return substr_replace($output, "", -2) . ']';
        } else {
            return false;
        }
    }

    /**
     * Simple test to see if array is multi-dimensional.
     *
     * @param array Array of values.
     *
     * @return bool Returns true is first element in the array is an array,
     *              otherwise false.
     */
    public static function arrayIsMulti($array)
    {
        if (is_array($array)) {
            if (count(array_filter($array, 'is_array')) > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Simple test to see if array values are of specified type.
     *
     * @param array Array of values.
     * @param string Type to check
     * @param string Named class, if type == 'class'
     *
     * @return bool Returns true is all values match type, otherwise false.
     */
    public static function arrayValuesCheck($array, $type, $className = '')
    {
        $status = true;

        if (is_array($array) && is_string($type)) {
            if ($type === 'class' && is_string($className) && ! empty($className)) {
                foreach ($array as $item) {
                    if (! is_null($item)) {
                        if (! is_object($item)) {
                            $status = false;
                        } else {
                            $class = new \ReflectionClass($item);

                            if (! preg_match("/{$className}/", $class->getShortname())) {
                                $status = false;
                            }
                        }
                    }
                }
            } else {
                foreach ($array as $item) {
                    $function = 'is_' . $type;

                    if (function_exists($function)) {
                        if ($function($item) === false) {
                            $status = false;
                        }
                    } else {
                        $status = false;
                    }
                }
            }
        } else {
            $status = false;
        }

        return $status;
    }

    /**
     * Tests input for valid int or percent
     *
     * Valid int = 5 or 12
     * Valid percent = 32% or 100%
     *
     * @param mixed Integer or string.
     *
     * @return bool Returns true if valid in or percent, otherwise false.
     */
    public static function isIntOrPercent($val)
    {
        if (is_int($val) === true) {
            return true;
        } elseif (is_string($val) === true) {
            if (ctype_digit($val) === true) {
                return true;
            } else {
                if ($val[strlen($val) - 1] == '%') {
                    $tmp = str_replace('%', '', $val);

                    if (ctype_digit((string) $tmp) === true) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    /**
     * Test if a number is between two other numbers.
     *
     * Pass in the number to test, the lower limit and upper limit.
     * Defaults to including the limits with <= & >=, set to false to exclude
     * the limits with < & >
     *
     * @param mixed number to test
     * @param mixed lower limit
     * @param mixed upper limit
     * @param bool whether to include limits
     *
     * @return bool
     */
    public static function between($lower, $test, $upper, $includeLimits = true)
    {
        $lowerCheck = (is_int($lower) || is_float($lower) ? true : false);
        $testCheck  = (is_int($test)  || is_float($test)  ? true : false);
        $upperCheck = (is_int($upper) || is_float($upper) ? true : false);

        if ($lowerCheck && $testCheck && $upperCheck && is_bool($includeLimits)) {
            if ($includeLimits === true) {
                return ($test >= $lower && $test <= $upper) ? true : false;
            } else {
                return ($test > $lower && $test < $upper) ? true : false;
            }
        } else {
            return false;
        }
    }

    /**
     * Checks if variable is a non-empty string
     *
     * @param  string $var
     *
     * @return bool
     */
    public static function nonEmptyString($var)
    {
        if (is_string($var) && ! empty($var)) {
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
     *
     * @return bool
     */
    public static function nonEmptyStringInArray($var, $arr)
    {
        if ((is_string($var) && ! empty($var)) && in_array($var, $arr)) {
            return true;
        } else {
            return false;
        }
    }
}
