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
            if ($type === 'class' && is_string($className) && ! empty($className)) {
                foreach ($array as $item) {
                    if (! is_null($item)) {
                        if (is_object($item) === true) {
                            $class = new \ReflectionClass($item);

                            if (preg_match("/{$className}/", $class->getShortname()) === true) {
                                $status = true;
                            }
                        }
                    }
                }
            } else {
                foreach ($array as $item) {
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
