<?php namespace Khill\Lavacharts\Configs;

/**
 * Vertical Axis Properties Object
 *
 * An object containing all the values for the axis which can be passed
 * into the chart's options.
 *
 *
 * @package    Lavacharts
 * @subpackage Configs
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */

class VerticalAxis extends Axis
{
    /**
     * Stores all the information about the vertical axis. All options can be
     * set either by passing an array with associative values for option =>
     * value, or by chaining together the functions once an object has been
     * created.
     *
     * @param  array        $config Configuration options for the VerticalAxis
     * @return VerticalAxis
     */
    public function __construct($config = array())
    {
        parent::__construct($this, $config);
    }
}
