<?php

namespace Khill\Lavacharts\Charts;

/**
 * Class Event
 *
 * @package Khill\Lavacharts\Charts
 */
class Event
{
    CONST FORMAT = <<<'EVENT'
        google.visualization.events.addListener(this.chart, "%s", function (event) {
            return lava.event(event, this, %s);
        }.bind(this));
EVENT;

    /**
     * Create a string of javascript wrapping a given event with a lava.js event.
     *
     * @param $event
     * @param $callback
     * @return string
     */
    public static function create($event, $callback)
    {
        return sprintf(self::FORMAT, $event, $callback);
    }

}
