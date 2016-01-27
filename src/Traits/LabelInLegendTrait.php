<?php

namespace Khill\Lavacharts\Traits;

trait LabelInLegendTrait
{
    /**
     * Whether the trendline should have a legend entry or not.
     *
     * @param  bool $label
     * @return \Khill\Lavacharts\Charts\Chart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function labelInLegend($label)
    {
        return $this->setBoolOption(__FUNCTION__, $label);
    }
}
