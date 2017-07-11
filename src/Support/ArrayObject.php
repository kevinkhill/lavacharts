<?php

namespace Khill\Lavacharts\Support;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Khill\Lavacharts\Support\Contracts\Arrayable;

abstract class ArrayObject implements ArrayAccess, Arrayable, Countable, IteratorAggregate
{
    /**
     * Returns the string name of a class property.
     *
     * This will be used to implement all the interfaces.
     *
     * @return string
     */
    abstract public function getArrayAccessProperty();

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->{$this->getArrayAccessProperty()};
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->toArray());
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->toArray());
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
        return isset($this->toArray()[$offset]);
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
        return $this->offsetExists($offset) ? $this->toArray()[$offset] : null;
    }

    private function &getArrayAccessPropertyRef()
    {
        return $this->{$this->getArrayAccessProperty()};
    }
}
