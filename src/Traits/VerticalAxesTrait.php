<?php

namespace Khill\Lavacharts\Traits;

use \Khill\Lavacharts\Utils;

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
     * @param  array $axes Array of VerticalAxis objects
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function vAxes($axes)
    {
        if (Utils::arrayValuesCheck($axes, 'class', 'VerticalAxis') === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'array',
                'of VerticalAxis Objects'
            );
        }

        return $this->addOption([__FUNCTION__ => $axes]);
    }
}
