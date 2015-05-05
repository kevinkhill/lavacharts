<?php namespace Khill\Lavacharts\Traits;

trait PointShapeTrait
{
    /**
     * The shape of individual data elements:
     *
     * 'circle', 'triangle', 'square', 'diamond', 'star', or 'polygon'.
     *
     * @see    https://developers.google.com/chart/interactive/docs/points
     * @param  string             $shape
     * @throws InvalidConfigValue
     * @return AreaChart
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
                'string'
            );
        }

        return  $this->addOption([__FUNCTION__ => $shape]);
    }
}
