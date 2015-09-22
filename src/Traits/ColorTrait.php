<?php

namespace Khill\Lavacharts\Traits;

trait ColorTrait
{
    /**
     * Sets the color for the object carrying this trait.
     *
     * @param  string $color
     * @return \Khill\Lavacharts\Charts\Chart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function color($color)
    {
        return $this->setStringOption(__FUNCTION__, $color);
    }
}
