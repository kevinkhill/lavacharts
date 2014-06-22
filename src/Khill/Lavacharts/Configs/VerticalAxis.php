<?php namespace Khill\Lavacharts\Configs;

/**
 * Vertical Axis Properties Object
 *
 * An object containing all the values for the axis which can be passed
 * into the chart's options.
 *
 *
 * @category  Class
 * @package   Khill\Lavacharts\Configs
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2014, KHill Designs
 * @link      https://github.com/kevinkhill/LavaCharts GitHub Repository Page
 * @link      http://kevinkhill.github.io/LavaCharts/ GitHub Project Page
 * @license   http://opensource.org/licenses/MIT MIT
 */

class VerticalAxis extends Axis
{
    /**
     * Stores all the information about the vertical axis. All options can be
     * set either by passing an array with associative values for option =>
     * value, or by chaining together the functions once an object has been
     * created.
     *
     * @param array Configuration options for the vAxis
     *
     * @return VerticalAxis
     */
    public function __construct($config = array())
    {
        return parent::__construct($config);
    }
}
