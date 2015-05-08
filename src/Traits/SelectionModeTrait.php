<?php namespace Khill\Lavacharts\Traits;

use \Khill\Lavacharts\Utils;

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

        if (in_array($selectionMode, $values, true) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string',
                'must be one of '.Utils::arrayToPipedString($values)
            );
        }

        return $this->addOption([__FUNCTION__ => $selectionMode]);
    }
}
