<?php namespace Khill\Lavacharts\Traits;

trait PointSizeTrait
{
    /**
     * Diameter of displayed points in pixels.
     *
     * Use zero to hide all points. You can override values for individual
     * series using the series property.
     *
     * @param  int                $size
     * @throws InvalidConfigValue
     * @return AreaChart
     */
    public function pointSize($size)
    {
        if (is_int($size) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return  $this->addOption(['pointSize' => $size]);
    }
}
