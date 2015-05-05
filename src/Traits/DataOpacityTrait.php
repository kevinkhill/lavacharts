<?php namespace Khill\Lavacharts\Traits;

trait DataOpacityTrait
{
    /**
     * The transparency of data points, with 1.0 being completely opaque and 0.0 fully transparent.
     *
     * @param  float    $dataOpacity
     * @return Chart
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

        return $this->addOption([__FUNCTION__ => $dataOpacity]);
    }
}
