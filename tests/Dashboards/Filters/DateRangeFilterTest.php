<?php

namespace Khill\Lavacharts\Tests\Dashboards\Filters;

use \Khill\Lavacharts\Dashboards\Filters\DateRange;
use \Khill\Lavacharts\Tests\ProvidersTestCase;

class DateRangeFilterTest extends ProvidersTestCase
{
    public function testSettingColumnIndexWithConstructor()
    {
        $dateRangeFilter = new DateRange(2);

        $this->assertEquals(2, $dateRangeFilter->filterColumnIndex);
    }

    public function testSettingColumnLabelWithConstructor()
    {
        $dateRangeFilter = new DateRange('revenue');

        $this->assertEquals('revenue', $dateRangeFilter->filterColumnLabel);
    }

    /**
     * @depends testSettingColumnLabelWithConstructor
     */
    public function testGetTypeMethodAndStaticReferences()
    {
        $dateRangeFilter = new DateRange('donuts');

        $this->assertEquals('DateRangeFilter', DateRange::TYPE);
        $this->assertEquals('DateRangeFilter', $dateRangeFilter::TYPE);
        $this->assertEquals('DateRangeFilter', $dateRangeFilter->getType());
    }
}
