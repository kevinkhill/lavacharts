<?php

namespace Khill\Lavacharts\Support\Traits;

use ArrayIterator;


/**
 * Trait DynamicArrayAccessTrait
 *
 * Trait for adding the methods for ArrayAccess based on the DynamicArrayAccess interface.
 *
 * @package   Khill\Lavacharts\Support\Traits
 * @since     4.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 *
 * @method getArrayAccessProperty
 */
trait IterableArrayAccessTrait
{
    /**
     * @return int
     */
    public function count()
    {
        return count($this->{$this->getArrayAccessProperty()});
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->{$this->getArrayAccessProperty()});
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->{$this->getArrayAccessProperty()}[] = $value;
        } else {
            $this->{$this->getArrayAccessProperty()}[$offset] = $value;
        }
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->{$this->getArrayAccessProperty()}[$offset]);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->{$this->getArrayAccessProperty()}[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->{$this->getArrayAccessProperty()}[$offset] : null;
    }
}
