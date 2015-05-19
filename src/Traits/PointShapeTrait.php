<?php

namespace Khill\Lavacharts\Traits;

use \Khill\Lavacharts\Utils;

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

        if (in_array($shape, $values, true) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string',
                'Accepted values include '.Utils::arrayToPipedString($values)
            );
        }

        return  $this->addOption([__FUNCTION__ => $shape]);
    }
}
