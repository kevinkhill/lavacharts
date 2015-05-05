<?php namespace Khill\Lavacharts\Traits;

trait ReverseCategoriesTrait
{
    /**
     * If set to true, will draw series from bottom to top. The default is to draw top-to-bottom.
     *
     * @param  bool               $reverseCategories
     * @throws InvalidConfigValue
     * @return BarChart
     */
    public function reverseCategories($reverseCategories)
    {
        if (is_bool($reverseCategories) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'bool'
            );
        }

        return $this->addOption([__FUNCTION__ => $reverseCategories]);
    }
}
