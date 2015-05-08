<?php

namespace Khill\Lavacharts\Traits;

use \Khill\Lavacharts\Configs\ColorAxis;

trait ColorAxisTrait
{
    /**
     * An object that specifies a mapping between color column values
     * and colors or a gradient scale.
     *
     * @param  \Khill\Lavacharts\Configs\ColorAxis $colorAxis
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function colorAxis(ColorAxis $colorAxis)
    {
        return $this->addOption($colorAxis->toArray(__FUNCTION__));
    }
}
