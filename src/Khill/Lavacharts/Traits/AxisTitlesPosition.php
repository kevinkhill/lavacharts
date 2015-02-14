<?php namespace Khill\Lavacharts\Traits;

use Khill\Lavacharts\Utils;
use Khill\Lavacharts\Exceptions\InvalidConfigValue;

trait AxisTitlesPosition
{
    /**
     * Where to place the axis titles, compared to the chart area. Supported values:
     * in   - Draw the axis titles inside the the chart area.
     * out  - Draw the axis titles outside the chart area.
     * none - Omit the axis titles.
     *
     * @param string $position The position of the axis titles
     *
     * @return Chart
     */
    public function axisTitlesPosition($position)
    {
        $values = array(
            'in',
            'out',
            'none'
        );

        if (in_array($position, $values)) {
            $this->addOption(array('axisTitlesPosition' => $position));
        } else {
            throw new InvalidConfigValue(
                $this->className,
                __FUNCTION__,
                'string',
                'with a value of '.Utils::arrayToPipedString($values)
            );
        }

        return $this;
    }
}
