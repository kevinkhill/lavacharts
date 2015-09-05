<?php

namespace Khill\Lavacharts\Traits;



trait PointShapeTrait
{
    /**
     * The shape of individual data elements.
     *
     * 'circle', 'triangle', 'square', 'diamond', 'star', or 'polygon'.
     *
     * @see    https://developers.google.com/chart/interactive/docs/points
     * @param  string $shape Accepted values [circle|triangle|square|diamond|star|polygon]
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function pointShape($shape)
    {
        $values = [
            'circle',
            'triangle',
            'square',
            'diamond',
            'star',
            'polygon'
        ];

        return  $this->setStringInArrayOption(__FUNCTION__, $shape, $values);
    }
}
