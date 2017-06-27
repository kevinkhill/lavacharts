<?php

namespace Khill\Lavacharts\Support\Contracts;

use JsonSerializable;

/**
 * Javascriptable Interface
 *
 * Classes that implement this method can transform to valid Javascript.
 *
 * @package   Khill\Lavacharts\Support\Contracts
 * @since     3.2.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
interface Javascriptable extends JsonSerializable
{
    /**
     * Returns customized Javascript output.
     *
     * @return string
     */
    public function toJavascript();

    /**
     * Returns the instance as Javascript.
     *
     * @return string
     */
    public function jsonSerialize();
}
