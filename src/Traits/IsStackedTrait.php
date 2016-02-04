<?php

namespace Khill\Lavacharts\Traits;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

trait IsStackedTrait
{
    /**
     * If set to true, stacks the elements for all series at each domain value.
     *
     * Note: In Column, Area, and SteppedArea charts, Google Charts reverses the order
     *  of legend items to better correspond with the stacking of the series elements
     *  (E.g. series 0 will be the bottom-most legend item). This does not apply to Bar Charts.
     *
     * The isStacked option also supports 100% stacking, where the stacks
     * of elements at each domain value are rescaled to add up to 100%.
     *
     * The options for isStacked are:
     *
     * false — elements will not stack. This is the default option.
     * true — stacks elements for all series at each domain value.
     *        'percent' — stacks elements for all series at each domain value
     *        and rescales them such that they add up to 100%, with each element's
     *        value calculated as a percentage of 100%.
     * 'relative' — stacks elements for all series at each domain value
     *              and rescales them such that they add up to 1, with each element's
     *              value calculated as a fraction of 1.
     * 'absolute' — functions the same as isStacked: true.
     *
     * For 100% stacking, the calculated value for each element will appear
     *  in the tooltip after its actual value.
     *
     * The target axis will default to tick values based on the relative
     * 0-1 scale as fractions of 1 for 'relative', and 0-100% for 'percent'
     * (Note: when using the 'percent' option, the axis/tick values are
     * displayed as percentages, however the actual values are the relative
     *  0-1 scale values. This is because the percentage axis ticks are the
     *  result of applying a format of "#.##%" to the relative 0-1 scale values.
     *  When using isStacked: 'percent', be sure to specify any ticks/gridlines
     * using the relative 0-1 scale values). You can customize the gridlines/tick
     * values and formatting using the appropriate hAxis/vAxis options.
     *
     * 100% stacking only supports data values of type number, and must have a baseline of zero.
     *
     * @param  bool|string $isStacked
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function isStacked($isStacked)
    {
        $values =[
            'relative',
            'absolute',
            'percent'
        ];

        if (is_bool($isStacked) === true) {
            return $this->setBoolOption(__FUNCTION__, $isStacked);
        } elseif (is_string($isStacked) === true) {
            return $this->setStringInArrayOption(__FUNCTION__, $isStacked, $values);
        } else {
            throw new InvalidConfigValue(
                static::TYPE . '->' . __FUNCTION__,
                'bool|string',
                'Whose value is one of '.Utils::arrayToPipedString($values)
            );
        }
    }
}
