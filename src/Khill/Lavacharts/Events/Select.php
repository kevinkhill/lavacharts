<?php namespace Khill\Lavacharts\Events;

/**
 * Select Event Object
 *
 * The base class for the individual event objects, providing common
 * functions to the child objects.
 *
 *
 * @package    Lavacharts
 * @subpackage Events
 * @since      v2.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */

class Select extends Event
{
    const TYPE = 'select';

    /**
     * Builds the Select Event object.
     *
     * @param  string              $c Callback function name.
     * @throws InvalidConfigValue
     * @return Select
     */
    public function __construct($c)
    {
        parent::__construct($c);
    }
}
