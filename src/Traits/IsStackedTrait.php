<?php

namespace Khill\Lavacharts\Traits;

trait IsStackedTrait
{
    /**
     * If set to true, series elements are stacked.
     *
     * @param  bool $isStacked
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function isStacked($isStacked)
    {
        return $this->setBoolOption(__FUNCTION__, $isStacked);
    }
}
