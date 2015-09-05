<?php

namespace Khill\Lavacharts\Traits;



trait FocusTargetTrait
{
    /**
     * The type of the entity that receives focus on mouse hover.
     *
     * Also affects which entity is selected by mouse click, and which data table
     * element is associated with events. Can be one of the following:
     *  'datum'    - Focus on a single data point. Correlates to a cell in the data table.
     *  'category' - Focus on a grouping of all data points along the major axis.
     *               Correlates to a row in the data table.
     *
     * In focusTarget 'category' the tooltip displays all the category values.
     * This may be useful for comparing values of different series.
     *
     * @since  2.4.1
     * @param  string $focusTarget Accepted values [datum|category]
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function focusTarget($focusTarget)
    {
        $values = [
            'datum',
            'category'
        ];

        return $this->setStringInArrayOption(__FUNCTION__, $focusTarget, $values);
    }
}
