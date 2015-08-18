<?php

namespace Khill\Lavacharts\Traits;

use \Khill\Lavacharts\Utils;

trait AreaOpacityTrait
{
    /**
     * The default opacity of the colored area under a chart series
     *
     * 0.0 is fully transparent and 1.0 is fully opaque. To specify opacity for
     * an individual series, set the areaOpacity value in the series property.
     *
     * @param  float $areaOpacity
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function areaOpacity($areaOpacity)
    {
        if (Utils::between(0.0, $areaOpacity, 1.0)  === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'float',
                'between 0.0 - 1.0'
            );
        }

        return $this->setOption(__FUNCTION__, $areaOpacity);
    }
}
