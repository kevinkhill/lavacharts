<?php

namespace Khill\Lavacharts\Traits;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

trait OpacityTrait
{
    /**
     * Sets the transparency of assigned object points
     *
     * 1.0 being completely opaque and 0.0 fully transparent.
     *
     * @param  float $opacity
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function opacity($opacity)
    {
        if (Utils::between(0.0, $opacity, 1.0) === false) {
            throw new InvalidConfigValue(
                static::TYPE . '->' . __FUNCTION__,
                'float',
                'between 0.0 - 1.0'
            );
        }

        return $this->setOption(__FUNCTION__, $opacity);
    }
}
