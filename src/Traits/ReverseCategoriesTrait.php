<?php

namespace Khill\Lavacharts\Traits;

trait ReverseCategoriesTrait
{
    /**
     * If set to true, will draw series from bottom to top.
     *
     * The default is to draw top-to-bottom.
     *
     * @param  boolean $reverseCategories
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function reverseCategories($reverseCategories)
    {
        return $this->setBoolOption(__FUNCTION__, $reverseCategories);
    }
}
