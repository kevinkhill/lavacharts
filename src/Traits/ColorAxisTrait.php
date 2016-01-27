<?php

namespace Khill\Lavacharts\Traits;

use \Khill\Lavacharts\Configs\ColorAxis;

trait ColorAxisTrait
{
    /**
     * An object that specifies a mapping between color column values
     * and colors or a gradient scale.
     *
     * @param  array $colorAxisConfig
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function colorAxis($colorAxisConfig)
    {
        return $this->setOption(__FUNCTION__, new ColorAxis($colorAxisConfig));
    }
}
