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
        if (is_bool($iframe) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'bool'
            );
        }

        return $this->addOption([__FUNCTION__ => $iframe]);
    }
}
