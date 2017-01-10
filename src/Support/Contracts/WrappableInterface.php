<?php

namespace Khill\Lavacharts\Support\Contracts;

/**
 * Interface WrappableInterface
 *
 * Classes that implement this can be wrapped for use in a Dashboard.
 *
 * @package   Khill\Lavacharts\Support\Contracts
 * @since     3.1.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
interface WrappableInterface
{
    /**
     * Returns the wrap type, either Control or Chart.
     *
     * @return string
     */
    public function getWrapType();
}
