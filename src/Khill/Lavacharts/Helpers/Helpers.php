<?php namespace Khill\Lavacharts\Helpers;

class Helpers
{

    /**
     * Magic method as an alias to is_a($object, $type)
     *
     * Called as Helpers::is_jsDate($object) or Helpers::is_textStyle($object)
     * to test if they are valid config objects.
     *
     * @param string $function
     * @param object $configObject
     * @return boolean
     */
    public static function __callStatic($function, $configObject)
    {
        if($function[2] == '_')
        {
            $functionParts = explode('_', $function);
            $is_a = $functionParts[1];

            if(is_object($configObject[0]))
            {
                $argumentParts = explode('\\', get_class($configObject[0]));
                $type = $argumentParts[count($argumentParts) - 1];

                return $type == $is_a;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    /**
     * Takes an array of values and ouputs them as a string between
     * brackets and separated by a pipe.
     *
     * @param array Array of default values
     * @return string Converted array to string.
     */
    public static function array_string($defaultValues)
    {
        if(is_array($defaultValues))
        {
            $output = '[ ';

            natcasesort($defaultValues);

            foreach($defaultValues as $value)
            {
                $output .= $value . ' | ';
            }

            return substr_replace($output, "", -2) . ']';
        } else {
            return FALSE;
        }
    }

    /**
     * Simple test to see if array is multi-dimensional.
     *
     * @param array Array of values.
     * @return boolean Returns TRUE is first element in the array is an array,
     * otherwise FALSE.
     */
    public static function array_is_multi($array)
    {
        if(is_array($array))
        {
            if(count(array_filter($array, 'is_array')) > 0)
            {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    /**
     * Simple test to see if array values are of specified type.
     *
     * @param array Array of values.
     * @param string Type to check
     * @param string Named class, if type == 'class'
     * @return boolean Returns TRUE is all values match type, otherwise FALSE.
     */
    public static function array_values_check($array, $type, $className = '')
    {
        $status = TRUE;

        if(is_array($array) && is_string($type))
        {
            if($type == 'class' && is_string($className) && !empty($className))
            {
                foreach($array as $item)
                {
                    $realClassName = self::get_real_class($item);

                    if($realClassName === FALSE)
                    {
                        $status = FALSE;
                    } else {
                        if($realClassName != $className)
                        {
                            $status = FALSE;
                        }
                    }
                }
            } else {
                foreach($array as $item)
                {
                    $function = 'is_'.$type;

                    if(function_exists($function))
                    {
                        if($function($item) === FALSE)
                        {
                            $status = FALSE;
                        }
                    } else {
                        $status = FALSE;
                    }
                }
            }
        } else {
            $status = FALSE;
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
     * @return boolean Returns TRUE if valid in or percent, otherwise FALSE.
     */
    public static function is_int_or_percent($val)
    {
        if(is_int($val) === TRUE)
        {
            return TRUE;
        } else if(is_string($val) === TRUE)
        {
            if(ctype_digit($val) === TRUE)
            {
                return TRUE;
            } else {
                if($val[strlen($val) - 1] == '%')
                {
                    $tmp = str_replace('%', '', $val);

                    if(ctype_digit((string) $tmp) === TRUE)
                    {
                        return TRUE;
                    } else {
                        return FALSE;
                    }
                } else {
                    return FALSE;
                }
            }
        } else {
            return FALSE;
        }
    }

    /**
     * Test if a number is between two other numbers.
     *
     * Pass in the number to test, the lower limit and upper limit.
     * Defaults to including the limits with <= & >=, set to FALSE to exclude
     * the limits with < & >
     *
     * @param mixed number to test
     * @param mixed lower limit
     * @param mixed upper limit
     * @param boolean whether to include limits
     * @return boolean
     */
    public static function between($lower, $test, $upper, $includeLimits = TRUE)
    {
        $lowerCheck = (is_int($lower) || is_float($lower) ? TRUE : FALSE);
        $testCheck  = (is_int($test)  || is_float($test)  ? TRUE : FALSE);
        $upperCheck = (is_int($upper) || is_float($upper) ? TRUE : FALSE);

        if($lowerCheck && $testCheck && $upperCheck && is_bool($includeLimits))
        {
            if($includeLimits === TRUE)
            {
                return ($test >= $lower && $test <= $upper) ? TRUE : FALSE;
            } else {
                return ($test > $lower && $test < $upper) ? TRUE : FALSE;
            }
        } else {
            return FALSE;
        }
    }

    /**
     * Gets object class name without namespace
     *
     * @param object $obj
     * @return string Class Name
     */
    public static function get_real_class($obj)
    {
        if(is_object($obj))
        {
            $classname = get_class($obj);

            if (preg_match('@\\\\([\w]+)$@', $classname, $matches)) {
                $classname = $matches[1];
            }

            return $classname;
        } else {
            return FALSE;
        }
    }

}
