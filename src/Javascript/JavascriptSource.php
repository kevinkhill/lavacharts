<?php

namespace Khill\Lavacharts\Javascript;

use Khill\Lavacharts\Support\Contracts\Javascriptable;

/**
 * JavascriptSource Class
 *
 * Define how an instance of an object will be converted to javascript source.
 *
 * @package       Khill\Lavacharts\Support
 * @since         4.0.0
 * @author        Kevin Hill <kevinkhill@gmail.com>
 * @copyright 2020 Kevin Hill
 * @link          http://github.com/kevinkhill/lavacharts GitHub Repository
 * @link          http://lavacharts.com                   Official Docs Site
 * @license       http://opensource.org/licenses/MIT      MIT
 */
abstract class JavascriptSource
{

    /**
     * Return a format string that will be used by vsprintf to convert the
     * extending class to javascript.
     *
     * @return string
     */
    abstract public function getJavascriptFormat();

    /**
     * Return an array of arguments to pass to the format string provided
     * by getJavascriptFormat().
     *
     * These variables will be used with vsprintf, and the format string
     * to convert the extending class to javascript.
     *
     * @return array
     */
    abstract public function getJavascriptSource();

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
