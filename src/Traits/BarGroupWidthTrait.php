<?php namespace Khill\Lavacharts\Traits;

use \Khill\Lavacharts\Utils;

trait BarGroupWidthTrait
{
    /**
     * The width of a group of bars, specified in either of these formats:
     *
     * - Pixels (e.g. 50).
     * - Percentage of the available width for each group (e.g. '20%'),
     *   where '100%' means that groups have no space between them.
     *
     * @param  int|string $barGroupWidth
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function barGroupWidth($barGroupWidth)
    {
        if (Utils::isIntOrPercent($barGroupWidth) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string | int',
                'must be a valid int or percent [ 50 | 65% ]'
            );
        }

        return $this->addOption([
            __FUNCTION__ => [
                'groupWidth' => $barGroupWidth
            ]
        ]);
    }
}
