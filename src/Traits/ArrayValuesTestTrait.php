<?php

namespace Khill\Lavacharts\Traits;

trait ArrayValuesTestTrait
{
    /**
     * Simple test to see if array values are of specified type.
     *
     * @param  array $array Array of values.
     * @param  string $type Type to check
     * @param  string $className Named class, if type == 'class'
     * @return boolean Returns true is all values match type, otherwise false.
     */
    protected function arrayValuesTest($array, $type, $className = '')
    {
        $status = false;

        if (is_array($array) && is_string($type)) {
            foreach ($array as $item) {
                if ($type == 'class') {
                    $class = new \ReflectionClass($item);

                    if ($className == $class->getShortname()) {
                        $status = true;
                    }
                } else {
                    $function = 'is_' . $type;

                    if (function_exists($function)) {
                        if ($function($item) === true) {
                            $status = true;
                        }
                    }
                }
            }
        }

        return $status;
    }
}
