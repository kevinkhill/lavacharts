<?php namespace Khill\Lavacharts\Traits;

use \Khill\Lavacharts\Utils;

trait CurveTypeTrait
{
    /**
     * Controls the curve of the lines when the line width is not zero.
     *
     * Can be one of the following:
     * 'none' - Straight lines without curve.
     * 'function' - The angles of the line will be smoothed.
     *
     * @param  string             $curveType
     * @throws InvalidConfigValue
     * @return Chart
     */
    public function curveType($curveType)
    {
        $values = [
            'none',
            'function'
        ];

        if (in_array($curveType, $values, true) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string',
                'with a value of '.Utils::arrayToPipedString($values)
            );
        }

        return $this->addOption([__FUNCTION__ => $curveType]);
    }
}
