<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Exceptions\InvalidEvent;
use \Khill\Lavacharts\Exceptions\InvalidEventCallback;

/**
 * EventManager Object
 *
 * This class keeps track of events and their respective callbacks for a chart or dashboard.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Configs
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class EventManager implements \Countable, \IteratorAggregate
{
    /**
     * Enabled chart events with callbacks.
     *
     * @var array
     */
    private $events = [];

    /**
     * The chart's defined events.
     *
     * @var array
     */
    private static $defaultEvents = [
        'animationfinish',
        'error',
        'mouseout',
        'mouseover',
        'ready',
        'select',
        'statechange'
    ];

    /**
     * Returns the number of events when the EventManager is counted.
     *
     * @access public
     * @implements Countable
     * @return int
     */
    public function count()
    {
        return count($this->events);
    }

    /**
     * Allows for the events to be traversed with foreach.
     *
     * @access public
     * @implements IteratorAggregate
     * @return int
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->events);
    }

    /**
     * Sets the callback for an event.
     *
     * @access public
     * @param  string $event
     * @param  string $callback
     * @throws \Khill\Lavacharts\Exceptions\InvalidEvent
     * @throws \Khill\Lavacharts\Exceptions\InvalidEventCallback
     */
    public function set($event, $callback)
    {
        $this->validEvent($event);

        if (Utils::nonEmptyString($callback) === false) {
            throw new InvalidEventCallback($callback);
        }

        $this->events[$event] = $callback;
    }

    /**
     * Retrieves the javascript callback for a chart event.
     *
     * @access public
     * @param  string $event
     * @return string
     * @throws \Khill\Lavacharts\Exceptions\InvalidEvent
     */
    public function getCallback($event)
    {
        $this->validEvent($event);

        return $this->events[$event];
    }

    /**
     * Checks whether the event is a valid chart event.
     *
     * @access private
     * @param  string $event
     * @return bool
     * @throws \Khill\Lavacharts\Exceptions\InvalidEvent
     */
    private function validEvent($event)
    {
        if (in_array($event, static::$defaultEvents) === false) {
            throw new InvalidEvent($event, static::$defaultEvents);
        }
    }
}
