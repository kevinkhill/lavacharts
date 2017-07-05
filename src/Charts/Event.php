<?php

namespace Khill\Lavacharts\Charts;

use Khill\Lavacharts\Javascript\JavascriptSource;

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
class Event extends JavascriptSource
{
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
     * {@inheritdoc}
     */
    public function toJavascript()
    {
        return sprintf($this->getFormatString(), $this->type, $this->callback);
    }

    /**
     * {@inheritdoc}
     */
    public function getFormatString()
    {
        /**
         * In the scope of the events, "this" is a reference to the lavachart class.
         */
        return <<<'EVENT'
            google.visualization.events.addListener(this.chart, "%s", function (event) {
                return lava.event(event, this, %s);
            }.bind(this));
EVENT;
    }
}
