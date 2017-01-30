<?php

namespace Khill\Lavacharts\Tests\Dashboards\Filters;

use \Khill\Lavacharts\Dashboards\Filters\CategoryFilter;
use \Khill\Lavacharts\Tests\ProvidersTestCase;

class CategoryFilterTest extends ProvidersTestCase
{
    /**
     * @covers \Khill\Lavacharts\Dashboards\Filters\Filter::filterColumnIndex
     */
    public function testSettingColumnIndexWithConstructor()
    {
        $categoryFilter = new CategoryFilter(2);

        $this->assertEquals(2, $categoryFilter['filterColumnIndex']);
    }

    /**
     * @covers \Khill\Lavacharts\Dashboards\Filters\Filter::filterColumnLabel
     */
    public function testSettingColumnLabelWithConstructor()
    {
        $categoryFilter = new CategoryFilter('cities');

        $this->assertEquals('cities', $categoryFilter['filterColumnLabel']);
    }

    /**
     * @depends testSettingColumnLabelWithConstructor
     * @covers \Khill\Lavacharts\Dashboards\Filters\CategoryFilter::getType
     */
    public function testGetTypeMethodAndStaticReferences()
    {
        $categoryFilter = new CategoryFilter('cities');

        $this->assertEquals('CategoryFilter', CategoryFilter::TYPE);
        $this->assertEquals('CategoryFilter', $categoryFilter->getType());
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidParamType
     */
    public function testSettingColumnIndexOrLabelWithConstructorAndBadValues()
    {
        new CategoryFilter([]);
        new CategoryFilter(1.2);
        new CategoryFilter(false);
        new CategoryFilter(new \stdClass());
    }

    /**
     * @depends testSettingColumnIndexWithConstructor
     * covers \Khill\Lavacharts\Dashboards\Filters\CategoryFilter::useFormattedValue
     */
    public function testUseFormattedValue()
    {
        $categoryFilter = new CategoryFilter(2);

        $categoryFilter->useFormattedValue(true);
        $this->assertTrue($categoryFilter['useFormattedValue']);

        $categoryFilter->useFormattedValue(false);
        $this->assertFalse($categoryFilter['useFormattedValue']);
    }

    /**
     * @depends testSettingColumnLabelWithConstructor
     * @covers \Khill\Lavacharts\Dashboards\Filters\CategoryFilter::jsonSerialize
     */
    public function testJsonSerialization()
    {
        $categoryFilter = new CategoryFilter('age', [
            'useFormattedValue' => true,
            'ui' => [
                'caption'     => 'Ages',
                'allowTyping' => true
            ]
        ]);

        $json = '{"useFormattedValue":true,"ui":{"caption":"Ages","allowTyping":true},"filterColumnLabel":"age"}';
        $this->assertEquals($json, json_encode($categoryFilter));
    }
}
