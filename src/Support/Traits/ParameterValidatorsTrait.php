<?php

namespace Khill\Lavacharts\Support\Traits;

use ArrayAccess;
use Countable;
use Traversable;

/**
 * Trait ArrayIsMultiTrait
 *
 * Provides a method for primitively checking that an array is multi dimensional.
 *
 * @package   Khill\Lavacharts\Support\Traits
 * @since     3.1.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
trait ParameterValidatorsTrait
{
    /**
     * Test to see if the variable can be treated as an array.
     *
     * Returns true if the variable is an array or can behave as an array. If not, returns false.
     *
     * @param  @param array|ArrayAccess $var
     * @return bool
     */
    protected function behavesAsArray($var)
    {
        $varIsArrayLike = ($var instanceof ArrayAccess && $var instanceof Countable);

        if (is_array($var) || $varIsArrayLike) {
            return true;
        }

        return false;
    }

    /**
     * Test to see if the variable is an array or a Traversable object.
     *
     * @param array|Traversable $var
     * @return bool
     */
    protected function isIterable($var)
    {
        if (is_array($var) || $var instanceof Traversable) {
            return true;
        }

        return false;
    }

    /**
     * Search the given array or iterable object for a value.
     *
     * Returns true if found, otherwise returns false.
     *
     * @param mixed             $value
     * @param array|Traversable $iterable
     * @return bool
     */
    protected function iterableValueSearch($value, $iterable)
    {
        if ($this->isIterable($iterable) === true) {
            if (is_array($iterable) !== true) {
                $iterable = iterator_to_array($iterable);
            }

            return in_array($value, $iterable);
        }

        return false;
    }

    /**
     * Test to see if the variable is string, and is not empty OR if an object can be a string and is not empty.
     *
     * Returns true if
     *
     * @param mixed $var String or object to check
     * @return bool
     */
    protected function validString($var)
    {
        $varIsStringy = (is_string($var) || method_exists($var, '__toString'));

        if ($varIsStringy && strlen((string) $var) > 0) {
            return true;
        }

        return false;

    }
}
