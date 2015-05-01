<?php namespace Khill\Lavacharts\Traits;

use Khill\Lavacharts\Utils;
use Khill\Lavacharts\Configs\Animation;

trait AnimationTrait
{
    /**
     * Set the animation options for a chart
     *
     * @param  Animation $animation Animation config object
     * @return Chart
     */
    public function animation(Animation $animation)
    {
        return $this->addOption($animation);
    }
}
