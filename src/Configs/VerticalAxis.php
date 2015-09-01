<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\Options;

/**
 * Vertical Axis ConfigObject
 *
 * An object containing all the values for the axis which can be passed
 * into the chart's options.
 *
 *
 * @package    Khill\Lavacharts
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
     * Type of JsonConfig object
     *
     * @var string
     */
    const TYPE = 'VerticalAxis';

    /**
     * Stores all the information about the vertical axis. All options can be
     * set either by passing an array with associative values for option =>
     * value, or by chaining together the functions once an object has been
     * created.
     *
     * @param  array $config Configuration options for the VerticalAxis
     * @return \Khill\Lavacharts\Configs\VerticalAxis
     */
    public function __construct($config = [])
    {
        $options = new Options($this->defaults);

        parent::__construct($options, $config);
    }
}
