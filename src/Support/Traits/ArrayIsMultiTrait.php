<?php

namespace Khill\Lavacharts\Support\Traits;

/**
 * Trait ArrayIsMultiTrait
 *
 * Provides a method for primitively checking that an array is multi dimensional.
 *
 * @package   Khill\Lavacharts\Support\Traits
 * @since     3.1.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2016, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
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
