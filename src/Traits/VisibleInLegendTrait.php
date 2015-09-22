<?php

namespace Khill\Lavacharts\Traits;

trait VisibleInLegendTrait
{
    /**
     * Whether the trendline should have a legend entry or not.
     *
     * @param  bool $visible
     * @return \Khill\Lavacharts\Charts\Chart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function visibleInLegend($visible)
    {
        return $this->setBoolOption(__FUNCTION__, $visible);
    }
}
