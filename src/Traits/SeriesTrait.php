<?php

namespace Khill\Lavacharts\Traits;

use \Khill\Lavacharts\Utils;

trait SeriesTrait
{
   /**
     * An array of objects, each describing the format of the corresponding series
     * in the chart.
     *
     * To use default values for a series, specify an null in the array.
     * If a series or a value is not specified, the global value will be used.
     *
     * @param  array $arr Array of Series objects
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function series($arr)
    {
        if (Utils::arrayValuesCheck($arr, 'class', 'Series') === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'array',
                'Series Objects'
            );
        }

        return $this->addOption([__FUNCTION__ => $arr]);
    }
}
