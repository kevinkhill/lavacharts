<?php

namespace Khill\Lavacharts\Traits;



trait SelectionModeTrait
{
    /**
     * When selectionMode is 'multiple', users may select multiple data points.
     *
     * @param  string $selectionMode Accepted values [single|multiple]
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function selectionMode($selectionMode)
    {
        $values = [
            'multiple',
            'single'
        ];

        return $this->setStringInArrayOption(__FUNCTION__, $selectionMode, $values);
    }
}
