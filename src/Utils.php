<?php

namespace Khill\Lavacharts;

class Utils
{
    /**
     * Takes an array of values and outputs them as a string between
     * brackets and separated by a pipe.
     *
     * @param array $defaultValues Array of default values
     * @return string Converted array to string.
     */
    public static function arrayToPipedString($defaultValues)
    {
        if (self::checkIterable($defaultValues)) {
            natcasesort($defaultValues);

            return '[ ' . implode(' | ', $defaultValues) . ' ]';
        } else {
            return false;
        }
    }

    /**
     * Simple test to see if array is multi-dimensional.
     *
     * @param array|\Traversable $variable A Traversable object or array filled with ArrayAccess implementing objects a simple array
     *
     * @return bool Returns true is first element in the array is an array,
     *              otherwise false.
     */
    public static function arrayIsMulti($variable)
    {
        if (self::checkIterable($variable)) {
            if (count(array_filter((array)$variable, 'self::checkArrayAccess')) > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Test to check if passed array/object is Traversable
     *
     * @param array|\Traversable $variable
     *
     * @return bool
     */
    public static function checkIterable($variable)
    {
        if (is_array($variable) || $variable instanceof \Traversable) {
            return true;
        }

        return false;
    }

    /**
     * @param array|\ArrayAccess $variable
     *
     * @return bool
     */
    public static function checkArrayAccess($variable)
    {
        if (is_array($variable) || ($variable instanceof \ArrayAccess && $variable instanceof \Countable)) {
            return true;
        }

        return false;
    }

    /**
     * Simple test to see if array values are of specified type.
     *
     * @param  array $array Array of values.
     * @param  string $type Type to check
     * @param  string $className Named class, if type == 'class'
     * @return boolean Returns true is all values match type, otherwise false.
     */
    public static function arrayValuesCheck($array, $type, $className = '')
    {
        $status = true;

        if (self::checkIterable($array) && is_string($type)) {
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
     * @param int|float $lower         The lower limit
     * @param int|float $test          The number to test
     * @param int|float $upper         The upper limit
     * @param bool      $includeLimits Set whether to include limits
     * @return bool
     */
    public static function between($lower, $test, $upper, $includeLimits = true)
    {
        $lowerCheck = is_numeric($lower) ? true : false;
        $testCheck  = is_numeric($test)  ? true : false;
        $upperCheck = is_numeric($upper) ? true : false;

        if ($lowerCheck && $testCheck && $upperCheck && is_bool($includeLimits)) {
            if ($includeLimits === true) {
                return ($test >= $lower && $test <= $upper) ? true : false;
            } else {
                return ($test >  $lower && $test <  $upper) ? true : false;
            }
        } else {
            return false;
        }
    }

    /**
     * Checks if variable is a non-empty string
     *
     * @param  string|object $var String or object implementing __toString
     * @return bool
     */
    public static function nonEmptyString($var)
    {
        if (
            (is_string($var) || (is_object($var) && method_exists($var, '__toString')))
            && strlen((string)$var) > 0
        ) {
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
        if (self::nonEmptyString($var) && self::checkInArrayForValue($var, $arr)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param mixed              $value
     * @param array|\Traversable $iterable
     *
     * @return bool
     */
    public static function checkInArrayForValue($value, $iterable)
    {
        // first check if it's tra
        if(self::checkIterable($iterable) === true){

            if (is_array($iterable) !== true) {
                // make the iterator an array
                $iterable = iterator_to_array($iterable);
            }

            // check if the value is in the array
            return in_array($value, $iterable);
        }

        return false;
    }
}
