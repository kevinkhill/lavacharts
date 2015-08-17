<?php

namespace Khill\Lavacharts\Tests\Dashboards\Filters;

use \Khill\Lavacharts\Dashboards\Filters\ChartRange;
use \Khill\Lavacharts\Tests\ProvidersTestCase;

class ChartRangeFilterTest extends ProvidersTestCase
{
    public function testSettingColumnIndexWithConstructor()
    {
        $chartRangeFilter = new ChartRange(2);

        $this->assertEquals(2, $chartRangeFilter->filterColumnIndex);
    }

    public function testSettingColumnLabelWithConstructor()
    {
        $chartRangeFilter = new ChartRange('lines');

        $this->assertEquals('lines', $chartRangeFilter->filterColumnLabel);
    }

    /**
     * @depends testSettingColumnLabelWithConstructor
     */
    public function testGetTypeMethodAndStaticReferences()
    {
        $chartRangeFilter = new ChartRange('donuts');

        $this->assertEquals('ChartRangeFilter', ChartRange::TYPE);
        $this->assertEquals('ChartRangeFilter', $chartRangeFilter::TYPE);
        $this->assertEquals('ChartRangeFilter', $chartRangeFilter->getType());
    }
}
