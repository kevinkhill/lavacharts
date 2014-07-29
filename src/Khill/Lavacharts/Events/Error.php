<?php namespace Khill\Lavacharts\Events;

/**
 * Error Event Object
 *
 * The base class for the individual event objects, providing common
 * functions to the child objects.
 *
 *
 * @package    Lavacharts
 * @subpackage Events
 * @since      v2.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2014, KHill Designs
 * @link       http://github.com/kevinkhill/LavaCharts GitHub Repository Page
 * @link       http://kevinkhill.github.io/LavaCharts GitHub Project Page
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Helpers\Helpers;
use Khill\Lavacharts\Exceptions\InvalidConfigValue;
use Khill\Lavacharts\Exceptions\InvalidConfigProperty;

class Error extends Event
{

    /**
     * Builds the Error Event object when passed an array of configuration options.
     *
     * @param  array                 $config Options for the Event
     * @throws InvalidConfigValue
     * @throws InvalidConfigProperty
     * @return Error
     */
    public function __construct($config = array())
    {
        parent::__construct($this, $config);
    }
}
