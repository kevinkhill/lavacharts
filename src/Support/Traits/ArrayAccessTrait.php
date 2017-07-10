<?php

namespace Khill\Lavacharts\Support\Traits;


trait ArrayAccessTrait
{
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
        $array = $this->{$this->getArrayAccessProperty()};

        return $this->offsetExists($offset) ? $array[$offset] : null;
    }
}
