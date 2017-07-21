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
 * @copyright (c) 2017, KHill Designs
 * @link          http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link          http://lavacharts.com                   Official Docs Site
 * @license       http://opensource.org/licenses/MIT      MIT
 *
 * @method getJavascriptFormat
 * @method getJavascriptSource
 */
trait ToJavascriptTrait
{
    /**
     * Using the format provided and the source variables, transform the instance
     * to javascript source.
     *
     * @return string
     */
    public function toJavascript()
    {
        return vsprintf($this->getJavascriptFormat(), $this->getJavascriptSource());
    }

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
