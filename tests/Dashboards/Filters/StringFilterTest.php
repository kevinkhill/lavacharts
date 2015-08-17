<?php

namespace Khill\Lavacharts\Tests\Dashboards\Filters;

use \Khill\Lavacharts\Dashboards\Filters\String;
use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Mockery as m;

class StringFilterTest extends ProvidersTestCase
{
    public function testSettingColumnIndexWithConstructor()
    {
        $stringFilter = new String(2);

        $this->assertEquals(2, $stringFilter->filterColumnIndex);
    }

    public function testSettingColumnLabelWithConstructor()
    {
        $stringFilter = new String('cities');

        $this->assertEquals('cities', $stringFilter->filterColumnLabel);
    }

    /**
     * @depends testSettingColumnLabelWithConstructor
     */
    public function testGetTypeMethodAndStaticReferences()
    {
        $stringFilter = new String('cities');

        $this->assertEquals('StringFilter', String::TYPE);
        $this->assertEquals('StringFilter', $stringFilter::TYPE);
        $this->assertEquals('StringFilter', $stringFilter->getType());
    }

    /**
     * @depends testSettingColumnLabelWithConstructor
     */
    public function testMatchTypeWithValidValues($matchType)
    {
        $stringFilter = new String('cities');

        $stringFilter->matchType('exact');
        $this->assertEquals('exact', $stringFilter->matchType);

        $stringFilter->matchType('prefix');
        $this->assertEquals('prefix', $stringFilter->matchType);

        $stringFilter->matchType('any');
        $this->assertEquals('any', $stringFilter->matchType);
    }

    /**
     * @depends testSettingColumnLabelWithConstructor
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMatchTypeWithInvalidValue()
    {
        $stringFilter = new String('cities');

        $stringFilter->matchType('Taco Bell');
    }

    /**
     * @depends testSettingColumnLabelWithConstructor
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMatchTypeWithBadTypes($badVals)
    {
        $stringFilter = new String('cities');

        $stringFilter->matchType($badVals);
    }

    /**
     * @depends testSettingColumnIndexWithConstructor
     */
    public function testCaseSensitive()
    {
        $stringFilter = new String(2);

        $stringFilter->caseSensitive(true);
        $this->assertTrue($stringFilter->caseSensitive);

        $stringFilter->caseSensitive(false);
        $this->assertFalse($stringFilter->caseSensitive);
    }

    /**
     * @depends testSettingColumnIndexWithConstructor
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testCaseSensitiveWithBadTypes($badVals)
    {
        $stringFilter = new String(2);

        $stringFilter->caseSensitive($badVals);
    }

    /**
     * @depends testSettingColumnIndexWithConstructor
     */
    public function testUseFormattedValue()
    {
        $stringFilter = new String(2);

        $stringFilter->useFormattedValue(true);
        $this->assertTrue($stringFilter->useFormattedValue);

        $stringFilter->useFormattedValue(false);
        $this->assertFalse($stringFilter->useFormattedValue);
    }

    /**
     * @depends testSettingColumnIndexWithConstructor
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testUseFormattedValueWithBadTypes($badVals)
    {
        $stringFilter = new String(2);

        $stringFilter->useFormattedValue($badVals);
    }

    /**
     * @depends testSettingColumnIndexWithConstructor
     */
    public function testUi()
    {
        $stringFilter = new String(2);
        $stringFilter->ui([
            'realtimeTrigger' => true
        ]);

        $this->assertInstanceOf('Khill\Lavacharts\Configs\UIs\StringUI', $stringFilter->ui);
    }
}
