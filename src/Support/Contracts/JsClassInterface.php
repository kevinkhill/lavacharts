<?php

namespace Khill\Lavacharts\Support\Contracts;

/**
 * Interface VisualizationInterface
 *
 * Classes that implement this provide a method for custom JSON output.
 *
 * @package   Khill\Lavacharts\Support\Contracts
 * @since     3.1.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
interface JsClassInterface
{
    /**
     * Returns the name of the Javascript object of a Google chart component.
     *
     * @return string
     */
    public function getJsClass();
}
