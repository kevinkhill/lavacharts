<?php

namespace Khill\Lavacharts\Charts;

class Event
{
    /**
     * Create a string of javascript wrapping a given event with a lava.js event.
     *
     * @param $event
     * @param $callback
     * @return string
     */
    public static function create($event, $callback)
    {
        return <<<EVENT
google.visualization.events.addListener(this.chart, "{$event}", function (event) {
    return lava.event(event, this, {$callback});'.PHP_EOL.
}.bind(this));
EVENT;
    }

}
