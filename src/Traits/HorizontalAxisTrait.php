<?php

namespace Khill\Lavacharts\Traits;

use \Khill\Lavacharts\Configs\HorizontalAxis;

trait HorizontalAxisTrait
{
    /**
     * An object with members to configure various horizontal axis elements.
     *
     * To specify properties of this property, create a new HorizontalAxis object,
     * set the values then pass it to this function or to the constructor.
     *
     * @param  array $horizontalAxisConfig
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function hAxis($horizontalAxisConfig)
    {
        return $this->setOption(__FUNCTION__, new HorizontalAxis($horizontalAxisConfig));
    }
}
