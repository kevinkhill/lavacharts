<?php

namespace Khill\Lavacharts\Traits;



trait AxisTitlesPositionTrait
{
    /**
     * Where to place the axis titles, compared to the chart area.
     *
     * Supported values:
     * in   - Draw the axis titles inside the the chart area.
     * out  - Draw the axis titles outside the chart area.
     * none - Omit the axis titles.
     *
     * @param  string $position Accepted values [in|out|none]
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function axisTitlesPosition($position)
    {
        $values = [
            'in',
            'out',
            'none'
        ];

        return $this->setStringInArrayOption(__FUNCTION__, $position, $values);
    }
}
