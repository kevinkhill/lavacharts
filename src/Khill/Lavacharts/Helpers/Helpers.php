<?php namespace Khill\Lavacharts\Helpers;

class Helpers
{

    /**
     * Magic method as a fake alias to is_a($object, $type)
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
        $tmp = '[ ';

        natcasesort($defaultValues);

        foreach($defaultValues as $k => $v)
        {
            $tmp .= $v . ' | ';
        }

        return substr_replace($tmp, "", -2) . ']';
    }

    /**
     * Simple test to see if array is multi-dimensional.
     *
     * @param array Array of values.
     * @return boolean Returns TRUE is first element in the array is an array,
     * otherwise FALSE.
     */
    public static function array_is_multi($arr)
    {
        $rv = array_filter($arr, 'is_array');

        if(count($rv) > 0)
        {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Simple test to see if array values are of specified type.
     *
     * @param array Array of values.
     * @return boolean Returns TRUE is all values match type, otherwise FALSE.
     */
    public static function array_values_check(&$arr, $type, $extra = NULL)
    {
        $status = TRUE;

        if(is_array($arr) && is_string($type))
        {
            if($type == 'class' && is_string($extra) && !empty($extra))
            {
                foreach($arr as $item)
                {
                    if(is_a($item, $extra) == FALSE)
                    {
                        $status = FALSE;
                        break;
                    }
                }
            } else {
                foreach($arr as $item)
                {
                    $function = 'is_'.$type;
                    if($function($item) == FALSE)
                    {
                        $status = FALSE;
                        break;
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
     * Simple public function to test if a number is between two other numbers.
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
    public static function between($test, $lower, $upper, $inclusive = TRUE)
    {
        if($inclusive === TRUE)
        {
            return ($test >= $lower && $test <= $upper) ? TRUE : FALSE;
        } else {
            return ($test > $lower && $test < $upper) ? TRUE : FALSE;
        }
    }

    /**
    * Obtains an object class name without namespaces
    *
    * @param object $obj
    * @return string Class Name
    */
   public static function get_real_class($obj)
    {
       $classname = get_class($obj);

       if (preg_match('@\\\\([\w]+)$@', $classname, $matches)) {
           $classname = $matches[1];
       }

       return $classname;
   }

}

?>