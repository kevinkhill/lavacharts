<?php

namespace Khill\Lavacharts\Support\Traits;

/**
 * ToJavascript Trait
 *
 * Define how an instance of an object will be converted to javascript source.
 *
 * @package       Khill\Lavacharts\Support\Traits
 * @since         4.0.0
 * @author        Kevin Hill <kevinkhill@gmail.com>
 * @copyright 2020 Kevin Hill
 * @link          http://github.com/kevinkhill/lavacharts GitHub Repository
 * @link          http://lavacharts.com                   Official Docs Site
 * @license       http://opensource.org/licenses/MIT      MIT
 *
 * @method getJavascriptFormat
 * @method getJavascriptSource
 */
trait ToJavascriptTrait
{
    /**
     * When accessing as a string, the instance will converted to javascript source.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJavascript();
    }
}
