<?php namespace Khill\Lavacharts\Traits;

use \Khill\Lavacharts\Utils;

trait EnableInteractivityTrait
{
    /**
     * Whether the chart throws user-based events or reacts to user interaction.
     *
     * If false, the chart will not throw 'select' or other interaction-based events
     * (but will throw ready or error events), and will not display hovertext or
     * otherwise change depending on user input.
     *
     * @param  bool  $enableInteractivity
     * @return Chart
     */
    public function enableInteractivity($enableInteractivity)
    {
        if (is_bool($enableInteractivity) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'bool'
            );
        }

        return $this->addOption([__FUNCTION__ => $enableInteractivity]);
    }
}
