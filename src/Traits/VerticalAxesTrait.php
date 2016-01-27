<?php

namespace Khill\Lavacharts\Traits;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Configs\VerticalAxis;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

trait VerticalAxesTrait
{
    /**
     * Specifies properties for individual vertical axes
     *
     * If the chart has multiple vertical axes. Each child object is a vAxis object,
     * and can contain all the properties supported by vAxis.
     * These property values override any global settings for the same property.
     *
     * To specify a chart with multiple vertical axes, first define a new axis using
     * series.targetAxisIndex, then configure the axis using vAxes.
     *
     * @param  array $vAxisConfigArray Array of VerticalAxis configuration arrays
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function vAxes($vAxisConfigArray)
    {
        if (Utils::arrayIsMulti($vAxisConfigArray) === false) {
            throw new InvalidConfigValue(
                static::TYPE . '->' . __FUNCTION__,
                'array',
                'With arrays of VerticalAxis options.'
            );
        }

        $vAxes = [];

        foreach ($vAxisConfigArray as $hAxisConfig) {
            $vAxes[] = new VerticalAxis($hAxisConfig);
        }

        return $this->setOption(__FUNCTION__, $vAxes);
    }
}
