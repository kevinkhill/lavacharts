<?php

namespace Khill\Lavacharts\Support\Traits;

/**
 * Trait ArrayValuesTestTrait
 *
 * Provides a method for checking all the values in an array are of the same type.
 *
 * @package   Khill\Lavacharts\Support\Traits
 * @since     3.1.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
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

    protected function isArrayOfClass(array $array, $class)
    {
        return array_walk($array, function ($value, $key, $class) {
            return $value instanceof $class;
        }, $class);
    }
}
