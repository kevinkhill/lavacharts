<?php

namespace Khill\Lavacharts\Traits;

use \Khill\Lavacharts\Utils;

trait DataOpacityTrait
{
    /**
     * The transparency of data points
     *
     * 1.0 being completely opaque and 0.0 fully transparent.
     *
     * @param  float $dataOpacity
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function dataOpacity($dataOpacity)
    {
        if (Utils::between(0.0, $dataOpacity, 1.0) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'float',
                'between 0.0 - 1.0'
            );
        }

        return $this->setOption(__FUNCTION__, $dataOpacity);
    }
}
