<?php

namespace Khill\Lavacharts\Tests\Dashboards\Filters;

use Khill\Lavacharts\Dashboards\Filters\Filter;
use Khill\Lavacharts\Dashboards\Filters\StringFilter;
use Khill\Lavacharts\Tests\ProvidersTestCase;

class FiltersTest extends ProvidersTestCase
{
    public function filterTypeProvider()
    {
        return [
            ['CategoryFilter'],
            ['ChartRangeFilter'],
            ['DateRangeFilter'],
            ['NumberRangeFilter'],
            ['StringFilter']
        ];
    }

    /**
     * @dataProvider filterTypeProvider
     */
    public function testConstructorWithColumnIndex($filterType)
    {
        $filter = 'Khill\Lavacharts\Dashboards\Filters\\'.$filterType;

        $filterClass = new $filter(2);

        $options = $this->inspect($filterClass, 'options');

        $this->assertEquals(2, $options['filterColumnIndex']);
    }

    /**
     * @dataProvider filterTypeProvider
     */
    public function testConstructorWithColumnLabel($filterType)
    {
        $filter = 'Khill\Lavacharts\Dashboards\Filters\\'.$filterType;

        $filterClass = new $filter('myColumnLabel');

        $options = $this->inspect($filterClass, 'options');

        $this->assertEquals('myColumnLabel', $options['filterColumnLabel']);
    }

    /**
     * @dataProvider filterTypeProvider
     * @depends testConstructorWithColumnIndex
     */
    public function testConstructorWithColumnIndexAndOptions($filterType)
    {
        $filter = 'Khill\Lavacharts\Dashboards\Filters\\'.$filterType;

        $filterClass = new $filter(2, ['floatOption' => 12.34]);

        $options = $this->inspect($filterClass, 'options');

        $this->assertEquals(12.34, $options['floatOption']);
    }

    /**
     * @dataProvider filterTypeProvider
     * @depends testConstructorWithColumnLabel
     */
    public function testGetWrapType($filterType)
    {
        $filter = 'Khill\Lavacharts\Dashboards\Filters\\'.$filterType;

        $filterClass = new $filter('myColumnLabel');

        $this->assertEquals('controlType', $filterClass->getWrapType());
    }

    /**
     * @depends testConstructorWithColumnIndex
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidFilterParam
     */
    public function testConstructorWithInvalidType()
    {
        new StringFilter(new \stdClass());
    }

    /**
     * @dataProvider filterTypeProvider
     * @depends testConstructorWithColumnIndex
     * @covers \Khill\Lavacharts\Dashboards\Filters\Filter::getType
     */
    public function testGetType($filterType)
    {
        $filter = 'Khill\Lavacharts\Dashboards\Filters\\'.$filterType;

        $filterClass = new $filter('myColumnLabel');

        $this->assertEquals($filterType, $filterClass->getType());
    }

    /**
     * @dataProvider filterTypeProvider
     * @depends testConstructorWithColumnIndex
     */
    public function testStaticCreateMethodWithColumnIndex($filterType)
    {
        $filterClass = Filter::create($filterType, 2);

        $options = $this->inspect($filterClass, 'options');

        $this->assertEquals(2, $options['filterColumnIndex']);
    }

    /**
     * @dataProvider filterTypeProvider
     * @depends testConstructorWithColumnLabel
     */
    public function testStaticCreateMethodWithColumnLabel($filterType)
    {
        $filterClass = Filter::create($filterType, 'myColumnLabel');

        $options = $this->inspect($filterClass, 'options');

        $this->assertEquals('myColumnLabel', $options['filterColumnLabel']);
    }

    /**
     * @dataProvider filterTypeProvider
     * @depends testStaticCreateMethodWithColumnLabel
     */
    public function testStaticCreateMethodWithColumnLabelAndOptions($filterType)
    {
        $filterClass = Filter::create($filterType, 'myColumnLabel', ['floatOption' => 12.34]);

        $options = $this->inspect($filterClass, 'options');

        $this->assertEquals(12.34, $options['floatOption']);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidFilterParam
     */
    public function testStaticCreateMethodWithInvalidTypeType($badType)
    {
        Filter::create($badType, 1);
    }

    /**
     * @dataProvider nonStringOrIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidFilterParam
     */
    public function testStaticCreateMethodWithInvalidIndex($badType)
    {
        Filter::create('StringFilter', $badType);
    }
}
