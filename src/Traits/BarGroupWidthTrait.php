<?php

namespace Khill\Lavacharts\Traits;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

trait BarGroupWidthTrait
{
    /**
     * The width of a group of bars, specified in either of these formats:
     * - Pixels (e.g. 50).
     * - Percentage of the available width for each group (e.g. '20%'),
     *   where '100%' means that groups have no space between them.
     *
     * @param  int|string $barGroupWidth
     * @return \Khill\Lavacharts\Charts\Chart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function barGroupWidth($barGroupWidth)
    {
        if (Utils::isIntOrPercent($barGroupWidth) === false) {
            throw new InvalidConfigValue(
                static::TYPE . '->' . __FUNCTION__,
                'int|string',
                'String only if representing a percent. "50%"'
            );
        }

        return $this->setOption(__FUNCTION__, [
            'groupWidth' => $barGroupWidth
        ]);
    }
}
