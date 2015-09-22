<?php

namespace Khill\Lavacharts\Traits;

trait CurveTypeTrait
{
    /**
     * Controls the curve of the lines when the line width is not zero.
     *
     * Can be one of the following:
     * 'none' - Straight lines without curve.
     * 'function' - The angles of the line will be smoothed.
     *
     * @param  string $curveType Accepted values [none|function]
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function curveType($curveType)
    {
        $values = [
            'none',
            'function'
        ];

        return $this->setStringInArrayOption(__FUNCTION__, $curveType, $values);
    }
}
