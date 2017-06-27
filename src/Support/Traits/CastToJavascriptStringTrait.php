<?php

namespace Khill\Lavacharts\Support\Traits;

/**
 * ToJavascript Trait
 *
 * When an instance is serialized to JSON, then it will convert to javascript.
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
trait CastToJavascriptStringTrait
{
    /**
     * When accessing an event as a string, it will be converted to javascript.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJavascript();
    }
}
