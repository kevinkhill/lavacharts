<?php

namespace Khill\Lavacharts\Traits;

trait ForceIFrameTrait
{
    /**
     * Draws the chart inside an inline frame.
     *
     * Note that on IE8, this option is ignored; all IE8 charts are drawn in i-frames.
     *
     * @param  boolean $iframe
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function forceIFrame($iframe)
    {
        return $this->setBoolOption(__FUNCTION__, $iframe);
    }
}
