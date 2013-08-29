<?php namespace Khill\Lavacharts\Configs;
/**
 * Vertical Axis Properties Object
 *
 * An object containing all the values for the axis which can be passed
 * into the chart's options.
 *
 *
 * @author Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2013, KHill Designs
 * @link https://github.com/kevinkhill/Codeigniter-gCharts GitHub Repository Page
 * @link http://kevinkhill.github.io/Codeigniter-gCharts/ GitHub Project Page
 * @license http://opensource.org/licenses/MIT MIT
 */

class vAxis extends Axis
{
    /**
     * Stores all the information about the vertical axis. All options can be
     * set either by passing an array with associative values for option =>
     * value, or by chaining together the functions once an object has been
     * created.
     *
     * @param array Configuration options for the vAxis
     * @return \axis
     */
    public function __construct($config = array())
    {
        return parent::__construct($config);
    }

}
