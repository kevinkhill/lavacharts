<?php

namespace Khill\Lavacharts\Traits;



trait ThemeTrait
{
    /**
     * A theme is a set of predefined option values that work together to achieve a specific chart
     * behavior or visual effect.
     *
     * Currently only one theme is available:
     *  'maximized' - Maximizes the area of the chart, and draws the legend and all of the
     *                labels inside the chart area. Sets the following options:
     *
     * chartArea: {width: '100%', height: '100%'},
     * legend: {position: 'in'},
     * titlePosition: 'in', axisTitlesPosition: 'in',
     * hAxis: {textPosition: 'in'}, vAxis: {textPosition: 'in'}
     *
     * @param  string $theme
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function theme($theme)
    {
        $values = [
            'maximized'
        ];

        return $this->setStringInArrayOption(__FUNCTION__, $theme, $values);
    }
}
