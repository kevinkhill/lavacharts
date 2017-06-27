<?php

namespace Khill\Lavacharts\Charts;

use Khill\Lavacharts\Support\Contracts\Javascriptable;
use Khill\Lavacharts\Support\Traits\CastsToJavascriptTrait as CastsToJavascript;

/**
 * Class Event
 *
 * @package       Khill\Lavacharts\Charts
 * @since         3.2.0
 * @author        Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link          http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link          http://lavacharts.com                   Official Docs Site
 * @license       http://opensource.org/licenses/MIT      MIT
 */
class Event implements Javascriptable
{
    use CastsToJavascript;

    /**
     * Format string for creating the events javascript
     */
    const FORMAT = <<<'EVENT'
        google.visualization.events.addListener(this.chart, "%s", function (event) {
            return lava.event(event, this, %s);
        }.bind(this));
EVENT;

    /**
     * Event type
     *
     * @var string
     */
    private $type;

    /**
     * Javascript callback name
     *
     * @var string
     */
    private $callback;

    /**
     * Event constructor.
     *
     * @param string $type
     * @param string $callback
     */
    function __construct($type, $callback)
    {
        $this->type = $type;
        $this->callback = $callback;
    }

    /**
     * Returns the object as a string of valid javascript.
     *
     * @return string
     */
    public function toJavascript()
    {
        return sprintf(self::FORMAT, $this->type, $this->callback);
    }
}
