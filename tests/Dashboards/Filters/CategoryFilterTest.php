<?php

namespace Khill\Lavacharts\Tests\Dashboards\Filters;

use \Khill\Lavacharts\Dashboards\Filters\Category;
use \Khill\Lavacharts\Tests\ProvidersTestCase;

class CategoryFilterTest extends ProvidersTestCase
{
    /**
     * @covers \Khill\Lavacharts\Dashboards\Filters\Filter::filterColumnIndex
     */
    public function testSettingColumnIndexWithConstructor()
    {
        $categoryFilter = new Category(2);

        $this->assertEquals(2, $categoryFilter->filterColumnIndex);
    }

    /**
     * @covers \Khill\Lavacharts\Dashboards\Filters\Filter::filterColumnLabel
     */
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
     * covers \Khill\Lavacharts\Dashboards\Filters\Category::useFormattedValue
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
     * @covers \Khill\Lavacharts\Dashboards\Filters\Filter::ui
     */
    public function testUiConfig()
    {
        $categoryFilter = new Category('age', [
            'ui' => [
                'caption'     => 'Ages',
                'allowTyping' => true
            ]
        ]);

        $this->assertInstanceOf('\Khill\Lavacharts\Configs\UIs\CategoryUI', $categoryFilter->ui);
    }

    /**
     * @depends testSettingColumnLabelWithConstructor
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testUiConfigWithBadTypes($badVals)
    {
        new Category('age', $badVals);
    }

    /**
     * @depends testSettingColumnLabelWithConstructor
     * @covers \Khill\Lavacharts\Dashboards\Filters\Category::jsonSerialize
     */
    public function testJsonSerialization()
    {
        $categoryFilter = new Category('age', [
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
