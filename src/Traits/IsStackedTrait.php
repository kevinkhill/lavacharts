<?php namespace Khill\Lavacharts\Traits;

trait IsStackedTrait
{
    /**
     * If set to true, series elements are stacked.
     *
     * @param  bool  $isStacked
     * @return Chart
     */
    public function isStacked($isStacked)
    {
        if (is_bool($isStacked) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'bool'
            );
        }

        return $this->addOption([__FUNCTION__ => $isStacked]);
    }
}
