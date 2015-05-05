<?php namespace Khill\Lavacharts\Traits;

use \Khill\Lavacharts\Configs\HorizontalAxis;

trait HorizontalAxisTrait
{
    /**
     * An object with members to configure various horizontal axis elements.
     *
     * To specify properties of this property, create a new HorizontalAxis object,
     * set the values then pass it to this function or to the constructor.
     *
     * @param  HorizontalAxis     $hAxis
     * @throws InvalidConfigValue
     * @return Chart
     */
    public function hAxis(HorizontalAxis $hAxis)
    {
        return $this->addOption($hAxis->toArray(__FUNCTION__));
    }
}
