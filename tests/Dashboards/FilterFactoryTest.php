<?php

namespace Khill\Lavacharts\Tests\Dashboards\Filters;

use Khill\Lavacharts\Dashboards\Filters\FilterFactory;
use Khill\Lavacharts\Tests\ProvidersTestCase;

class FilterFactoryTest extends ProvidersTestCase
{
    public function filterTypeProvider()
    {
        return [
            ['Category'],
            ['ChartRange'],
            ['DateRange'],
            ['NumberRange'],
            ['String']
        ];
    }

    /**
     * @dataProvider filterTypeProvider
     */
    public function testStaticCreateMethodWithColumnIndex($filterType)
    {
        $filterClass = FilterFactory::create($filterType, 2);

        $options = $this->inspect($filterClass, 'options');

        $this->assertEquals(2, $options['filterColumnIndex']);
    }

    /**
     * @dataProvider filterTypeProvider
     */
    public function testStaticCreateMethodWithColumnLabel($filterType)
    {
        $filterClass = FilterFactory::create($filterType, 'myColumnLabel');

        $options = $this->inspect($filterClass, 'options');

        $this->assertEquals('myColumnLabel', $options['filterColumnLabel']);
    }

    /**
     * @dataProvider filterTypeProvider
     * @depends testStaticCreateMethodWithColumnLabel
     */
    public function testStaticCreateMethodWithColumnLabelAndOptions($filterType)
    {
        $filterClass = FilterFactory::create($filterType, 'myColumnLabel', ['floatOption' => 12.34]);

        $options = $this->inspect($filterClass, 'options');

        $this->assertEquals(12.34, $options['floatOption']);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidFilterType
     */
    public function testStaticCreateMethodWithInvalidType($badType)
    {
        FilterFactory::create($badType, 1);
    }

    /**
     * @dataProvider nonStringOrIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidParamType
     */
    public function testStaticCreateMethodWithInvalidIndex($badType)
    {
        FilterFactory::create('String', $badType);
    }
}
