<?php

namespace Khill\Lavacharts\Traits;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Configs\Trendline;

trait TrendlinesTrait
{
    /**
     * Defines how the chart trendlines will be displayed.
     *
     * @param  array $trendlineConfigArray
     * @return \Khill\Lavacharts\Charts\Chart
     * @throws \Khill\Lavacharts\Traits\InvalidConfigValue
     */
    public function trendlines($trendlineConfigArray)
    {
        if (Utils::arrayIsMulti($trendlineConfigArray) === false) {
            throw new InvalidConfigValue(
                static::TYPE . '->' . __FUNCTION__,
                'array',
                'With arrays of Trendline options.'
            );
        }

        $trendlines = [];

        foreach ($trendlineConfigArray as $index => $trendlineConfig) {
            $trendlines[(string) $index] = new Trendline($trendlineConfig);
        }

        return $this->setOption(__FUNCTION__, $trendlines);
    }
}
