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
 * @copyright 2020 Kevin Hill
 * @link          http://github.com/kevinkhill/lavacharts GitHub Repository
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
}
