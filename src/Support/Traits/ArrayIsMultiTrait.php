<?php

namespace Khill\Lavacharts\Support\Traits;

trait ArrayIsMultiTrait
{
    /**
     * Simple test to see if array is multi-dimensional.
     *
     * @param  array $array
     * @return bool Returns true is first element in the array is an array, otherwise false.
     */
    protected function arrayIsMulti($array)
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
}
