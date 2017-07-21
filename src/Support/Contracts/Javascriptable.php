<?php

namespace Khill\Lavacharts\Support\Contracts;

/**
 * JavascriptSource Interface
 *
 * Define how an instance of an object will be converted to javascript source.
 *
 * @package       Khill\Lavacharts\Support\Contracts
 * @since         4.0.0
 * @author        Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link          http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link          http://lavacharts.com                   Official Docs Site
 * @license       http://opensource.org/licenses/MIT      MIT
 */
interface Javascriptable
{
    /**
     * Using the format provided and the source variables, transform the instance
     * to javascript source.
     *
     * @return string
     */
    public function toJavascript();

    /**
     * Return a format string that will be used by vsprintf to convert the
     * extending class to javascript.
     *
     * @return string
     */
    public function getJavascriptFormat();

    /**
     * Return an array of arguments to pass to the format string provided
     * by getJavascriptFormat().
     *
     * These variables will be used with vsprintf, and the format string
     * to convert the extending class to javascript.
     *
     * @return array
     */
    public function getJavascriptSource();
}
