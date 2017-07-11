<?php

namespace Khill\Lavacharts\Support\Traits;

/**
 * Trait ArrayToJsonTrait
 *
 * This pattern of using the toArray method of json serialization is common.
 *
 *
 * @package   Khill\Lavacharts\Support\Traits
 * @since     3.2.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
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
