<?php

namespace Khill\Lavacharts\Traits;

trait PointSizeTrait
{
    /**
     * Diameter of displayed points in pixels.
     *
     * Use zero to hide all points. You can override values for individual
     * series using the series property.
     *
     * @param  integer $size
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function pointSize($size)
    {
        return  $this->setIntOption(__FUNCTION__, $size);
    }
}
