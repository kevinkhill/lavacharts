<?php

namespace Khill\Lavacharts\Events;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * Event Parent Object
 *
 * The base class for the individual event objects, providing common
 * functions to the child objects.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Events
 * @since      2.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class Event
{
    /**
     * Javascript callback function name.
     *
     * @var string
     */
    public $callback;

    /**
     * Builds the Event object.
     *
     * @param  string $c Name of Javascript callback function.
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function __construct($c)
    {
        if (Utils::nonEmptyString($c) === false) {
            throw new InvalidConfigValue(
                'an Event',
                'string'
            );
        }

        $this->callback = $c;
    }
}
