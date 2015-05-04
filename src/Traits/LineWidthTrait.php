<?php namespace Khill\Lavacharts\Traits;

trait LineWidthTrait
{
    /**
     * Data line width in pixels.
     *
     * Use zero to hide all lines and show only the points.
     * You can override values for individual series using the series property.
     *
     * @param  int                $width
     * @throws InvalidConfigValue
     * @return AreaChart
     */
    public function lineWidth($width)
    {
        if (is_int($width) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return $this->addOption([__FUNCTION__ => $width]);
    }
}
