<?php

namespace Khill\Lavacharts\Events;

/**
 * Callback Object
 *
 * A generic callback class used to define javascript callbacks to be used within charts.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Events
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class Callback extends Event
{
    /**
     * Javascript callback function name.
     *
     * @var string
     */
    public $callback;

    /**
     * Builds the Callback object.
     *
     * @param  string $c Name of Javascript callback function.
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function __construct($c)
    {
        parent::__construct($c);
    }
}
