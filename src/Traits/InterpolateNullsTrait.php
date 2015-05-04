<?php namespace Khill\Lavacharts\Traits;

trait InterpolateNullsTrait
{
    /**
     * Whether to guess the value of missing points.
     *
     * If true, it will guess the
     * value of any missing data based on neighboring points. If false, it will
     * leave a break in the line at the unknown point.
     *
     * @param  bool               $interpolateNulls
     * @throws InvalidConfigValue
     * @return AreaChart
     */
    public function interpolateNulls($interpolateNulls)
    {
        if (is_bool($interpolateNulls) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'bool'
            );
        }

        return $this->addOption([__FUNCTION__ => $interpolateNulls]);
    }
}
