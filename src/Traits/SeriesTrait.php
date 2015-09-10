<?php

namespace Khill\Lavacharts\Traits;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Configs\Series;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

trait SeriesTrait
{
    /**
     * An array of objects, each describing the format of the corresponding series
     * in the chart.
     * To use default values for a series, specify an null in the array.
     * If a series or a value is not specified, the global value will be used.
     *
     * @param  array $seriesConfigArray Array of Series options.
     * @return \Khill\Lavacharts\Charts\Chart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function series($seriesConfigArray)
    {
        if (Utils::arrayIsMulti($seriesConfigArray) === false) {
            throw new InvalidConfigValue(
                static::TYPE . '->' . __FUNCTION__,
                'array',
                'With arrays of Series options.'
            );
        }

        $series = [];

        foreach ($seriesConfigArray as $seriesConfig) {
            $series[] = new Series($seriesConfig);
        }

        return $this->setOption(__FUNCTION__, $series);
    }
}
