<?php

namespace Khill\Lavacharts\Tests\Dashboards\Filters;

use \Khill\Lavacharts\Dashboards\Filters\Category;
use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Mockery as m;

class CategoryFilterTest extends ProvidersTestCase
{
    public function testSettingColumnIndexWithConstructor()
    {
        $categoryFilter = new Category(2);

        $this->assertEquals(2, $categoryFilter->filterColumnIndex);
    }

    public function testSettingColumnLabelWithConstructor()
    {
        $categoryFilter = new Category('cities');

        $this->assertEquals('cities', $categoryFilter->filterColumnLabel);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testSettingColumnIndexOrLabelWithConstructorAndBadValues()
    {
        new Category([]);
        new Category(1.2);
        new Category(false);
        new Category(new \stdClass());
    }

    /**
     * @depends testSettingColumnIndexWithConstructor
     */
    public function testUseFormattedValue()
    {
        $categoryFilter = new Category(2);

        $categoryFilter->useFormattedValue(true);
        $this->assertTrue($categoryFilter->useFormattedValue);

        $categoryFilter->useFormattedValue(false);
        $this->assertFalse($categoryFilter->useFormattedValue);
    }

    /**
     * @dataProvider nonBoolProvider
     * @depends testSettingColumnIndexWithConstructor
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testUseFormattedValueWithBadTypes($badVals)
    {
        $categoryFilter = new Category(2);

        $categoryFilter->useFormattedValue($badVals);
    }

    /**
     * @depends testSettingColumnLabelWithConstructor
     *
     */
    public function testJsonSerialization()
    {
        $categoryFilter = new Category('age', [
            'useFormattedValue' => true,
            'ui' => [
                'caption'=>'Ages',
                'allowTyping'=>true
            ]
        ]);

        $json = '{"useFormattedValue":true,"ui":{"caption":"Ages","allowTyping":true},"filterColumnLabel":"age"}';
        $this->assertEquals($json, json_encode($categoryFilter));
    }
}
