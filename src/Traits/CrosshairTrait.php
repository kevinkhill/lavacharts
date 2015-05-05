<?php namespace Khill\Lavacharts\Traits;

use \Khill\Lavacharts\Configs\Crosshair;

trait CrosshairTrait
{
    /**
     * An object containing the crosshair properties for the chart..
     *
     * To specify properties of this property, create a new HorizontalAxis object,
     * set the values then pass it to this function or to the constructor.
     *
     * @param  Crosshair          $crosshair
     * @throws InvalidConfigValue
     * @return Chart
     */
    public function crosshair(Crosshair $crosshair)
    {
        return $this->addOption($crosshair->toArray());
    }
}
