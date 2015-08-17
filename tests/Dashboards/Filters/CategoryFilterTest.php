<?php

namespace Khill\Lavacharts\Tests\Dashboards\Filters;

use \Khill\Lavacharts\Dashboards\Filters\Category;
use \Khill\Lavacharts\Tests\ProvidersTestCase;

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
     * @depends testSettingColumnIndexWithConstructor
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testUseFormattedValueWithBadTypes($badVals)
    {
        $categoryFilter = new Category(2);

        $categoryFilter->useFormattedValue($badVals);
    }

    /**
     * @depends testSettingColumnLabelWithConstructor
     */
    public function testValues()
    {
        $categoryFilter = new Category('age');
        $categoryFilter->values([20,30,40]);

        $this->assertTrue(is_array($categoryFilter->values));
        $this->assertEquals([20,30,40], $categoryFilter->values);
    }

    /**
     * @depends testSettingColumnLabelWithConstructor
     */
    public function testUi()
    {
        $categoryFilter = new Category('age');
        $categoryFilter->ui([
            'caption'     => 'Ages',
            'allowTyping' => true
        ]);

        $this->assertInstanceOf('Khill\Lavacharts\Configs\UIs\CategoryUI', $categoryFilter->ui);
    }

    /**
     * @depends testSettingColumnLabelWithConstructor
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
