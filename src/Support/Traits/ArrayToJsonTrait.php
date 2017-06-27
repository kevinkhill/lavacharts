<?php

namespace Khill\Lavacharts\Support\Traits;

trait ArrayToJsonTrait
{
    /**
     * Convert the instance to a JSON string.
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this);
    }

    /**
     * Serialize the instance from the toArray() value.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
