<?php

namespace Khill\Lavacharts\Tests\Dashboards\Filters;

use \Khill\Lavacharts\Dashboards\Filters\DateRangeFilter;
use \Khill\Lavacharts\Tests\ProvidersTestCase;

class DateRangeFilterTest extends ProvidersTestCase
{
    public function testSettingColumnIndexWithConstructor()
    {
        $dateRangeFilter = new DateRangeFilter(2);

        $this->assertEquals(2, $dateRangeFilter->filterColumnIndex);
    }

    public function testSettingColumnLabelWithConstructor()
    {
        $dateRangeFilter = new DateRangeFilter('revenue');

        $this->assertEquals('revenue', $dateRangeFilter->filterColumnLabel);
    }

    /**
     * @depends testSettingColumnLabelWithConstructor
     * @covers \Khill\Lavacharts\Dashboards\Filters\DateRangeFilter::getType
     */
    public function testGetType()
    {
        $dateRangeFilter = new DateRangeFilter('donuts');

        $this->assertEquals('DateRangeFilter', $dateRangeFilter->getType());
    }
}
