<?php

namespace Khill\Lavacharts\Support\Contracts;

/**
 * Interface ScriptableInterface
 *
 * Classes that implement this provide a method for custom JSON output.
 *
 * @package   Khill\Lavacharts\Support\Contracts
 * @since     3.1.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2016, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
interface ScriptableInterface
{
    /**
     * Returns a customized Javascript representation of an object.
     *
     * @return string
     */
    public function toJavascript();
}
