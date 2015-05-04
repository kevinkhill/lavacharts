<?php namespace Khill\Lavacharts\Traits;

use Khill\Lavacharts\Utils;

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
     * @param  string             $position The position of the axis titles
     * @throws InvalidConfigValue
     * @return Chart
     */
    public function axisTitlesPosition($position)
    {
        $values = [
            'in',
            'out',
            'none'
        ];

        if (in_array($position, $values, true) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string',
                'with a value of '.Utils::arrayToPipedString($values)
            );
        }

        return $this->addOption([__FUNCTION__ => $position]);
    }
}
