<?php

namespace Khill\Lavacharts\Javascript;

use Khill\Lavacharts\Support\Contracts\Javascriptable;

/**
 * JavascriptSource Class
 *
 * @package       Khill\Lavacharts\Support
 * @since         3.2.0
 * @author        Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link          http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link          http://lavacharts.com                   Official Docs Site
 * @license       http://opensource.org/licenses/MIT      MIT
 */
abstract class JavascriptSource implements Javascriptable
{
    /**
     * Define how the class will be cast to javascript source when
     * the extending class is treated like a string.
     *
     * @return string
     */
    public abstract function toJavascript();

    /**
     * Return a format string that will be used by sprintf to convert the
     * extending class to javascript.
     *
     * @return string
     */
    public abstract function getFormatString();

    /**
     * When accessing as a string, the instance will converted to
     * javascript source.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJavascript();
    }
}
