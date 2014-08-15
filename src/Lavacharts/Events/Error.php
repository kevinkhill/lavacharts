<?php namespace Lavacharts\Events;

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
 * @link       http://github.com/kevinkhill/Lavacharts GitHub Repository Page
 * @link       http://kevinkhill.github.io/Lavacharts  GitHub Project Page
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Lavacharts\Helpers\Helpers;
use Lavacharts\Exceptions\InvalidConfigValue;
use Lavacharts\Exceptions\InvalidConfigProperty;

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
