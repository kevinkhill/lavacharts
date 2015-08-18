<?php

namespace Khill\Lavacharts\Traits;

use \Khill\Lavacharts\Configs\Crosshair;

trait CrosshairTrait
{
    /**
     * An object containing the crosshair properties for the chart..
     *
     * To specify properties of this property, create a new HorizontalAxis object,
     * set the values then pass it to this function or to the constructor.
     *
     * @param  array $crosshairConfig
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function crosshair($crosshairConfig)
    {
        return $this-setOption(__FUNCTION__, new Crosshair($crosshairConfig));
    }
}
