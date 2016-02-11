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
}
