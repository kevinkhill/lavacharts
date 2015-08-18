<?php

namespace Khill\Lavacharts\Traits;

trait InterpolateNullsTrait
{
    /**
     * Whether to guess the value of missing points.
     *
     * If true, it will guess the
     * value of any missing data based on neighboring points. If false, it will
     * leave a break in the line at the unknown point.
     *
     * @param  boolean $interpolateNulls
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function interpolateNulls($interpolateNulls)
    {
        return $this->setBoolOption(__FUNCTION__, $interpolateNulls);
    }
}
