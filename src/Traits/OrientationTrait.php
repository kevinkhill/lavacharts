<?php

namespace Khill\Lavacharts\Traits;

trait OrientationTrait
{
    /**
     * Sets the orientation of the chart.
     *
     * When set to 'vertical', rotates the axes of the chart so that (for instance)
     * a column chart becomes a bar chart, and an area chart grows rightward instead of up
     *
     * @param  boolean $orientation
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function orientation($orientation)
    {
        $values = [
            'horizontal',
            'vertical'
        ];

        return $this->setStringInArrayOption(__FUNCTION__, $orientation, $values);
    }
}
