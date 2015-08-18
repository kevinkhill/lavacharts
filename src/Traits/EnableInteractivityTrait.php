<?php

namespace Khill\Lavacharts\Traits;

trait EnableInteractivityTrait
{
    /**
     * Whether the chart throws user-based events or reacts to user interaction.
     *
     * If false, the chart will not throw 'select' or other interaction-based events
     * (but will throw ready or error events), and will not display hovertext or
     * otherwise change depending on user input.
     *
     * @param  boolean $enableInteractivity
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function enableInteractivity($enableInteractivity)
    {
        return $this->setBoolOption(__FUNCTION__, $enableInteractivity);
    }
}
