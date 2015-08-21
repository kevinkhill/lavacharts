<?php

namespace Khill\Lavacharts\Traits;

use \Khill\Lavacharts\Configs\Crosshair;

trait CrosshairTrait
{
    /**
     * An array containing the crosshair properties for the chart.
     *
     * @param  array $crosshairConfig
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function crosshair($crosshairConfig)
    {
        return $this->setOption(__FUNCTION__, new Crosshair($crosshairConfig));
    }
}
