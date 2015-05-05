<?php namespace Khill\Lavacharts\Traits;

use \Khill\Lavacharts\Utils;

trait HorizontalAxesTrait
{
    /**
     * Specifies properties for individual horizontal axes, if the chart has multiple horizontal axes.
     *
     * Each child object is a hAxis object, and can contain all the properties supported by hAxis.
     * These property values override any global settings for the same property.
     *
     * To specify a chart with multiple horizontal axes, first define a new axis using series.targetAxisIndex,
     * then configure the axis using hAxes.
     *
     * @param  array              $arr Array of HorizontalAxis objects
     * @throws InvalidConfigValue
     * @return Chart
     */
    public function hAxes($arr)
    {
        if (Utils::arrayValuesCheck($arr, 'class', 'HorizontalAxis') === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'array',
                'of HorizontalAxis Objects'
            );
        }

        return $this->addOption([__FUNCTION__ => $arr]);
    }
}
