<?php namespace Khill\Lavacharts\Traits;

use Khill\Lavacharts\Utils;

trait AreaOpacityTrait
{
    /**
     * The default opacity of the colored area under an area chart series, where
     * 0.0 is fully transparent and 1.0 is fully opaque. To specify opacity for
     * an individual series, set the areaOpacity value in the series property.
     *
     * @param  float              $opacity
     * @throws InvalidConfigValue
     * @return AreaChart
     */
    public function areaOpacity($opacity)
    {
        if (Utils::between(0.0, $opacity, 1.0, true) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'float',
                'where 0 < opacity < 1'
            );
        }

        return $this->addOption([__FUNCTION__ => $opacity]);
    }
}
