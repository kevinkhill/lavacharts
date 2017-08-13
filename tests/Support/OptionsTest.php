<?php

namespace Khill\Lavacharts\Tests\Support;

use Khill\Lavacharts\Support\Options;
use Khill\Lavacharts\Tests\ProvidersTestCase;

/**
 * @property Options options
 * @property string  optionsJson
 */
class OptionsTest extends ProvidersTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->optionsJson = '{"option1":"value1","option2":"value2","option3":"value3"}';

        $this->options = new Options([
            'option1' => 'value1',
            'option2' => 'value2',
            'option3' => 'value3'
        ]);
    }

    public function testStaticallyCreatingOptions()
    {
        $options = Options::create([
            'taco' => 'salad'
        ]);

        $this->assertInstanceOf(Options::class, $options);
    }

    public function testStaticallyGettingDefaultOptions()
    {
        $options = Options::getDefault();

        $this->assertTrue(is_array($options));
        $this->assertArrayHasKey('auto_run', $options);
        $this->assertArrayHasKey('locale', $options);
        $this->assertArrayHasKey('timezone', $options);
        $this->assertArrayHasKey('datetime_format', $options);
        $this->assertArrayHasKey('maps_api_key', $options);
        $this->assertArrayHasKey('responsive', $options);
        $this->assertArrayHasKey('debounce_timeout', $options);
    }

    public function testStaticallyGettingAvailableOptions()
    {
        $options = Options::getAvailable();

        $this->assertTrue(is_array($options));
        $this->assertContains('auto_run', $options);
        $this->assertContains('locale', $options);
        $this->assertContains('timezone', $options);
        $this->assertContains('datetime_format', $options);
        $this->assertContains('maps_api_key', $options);
        $this->assertContains('responsive', $options);
        $this->assertContains('debounce_timeout', $options);
    }

    public function testOptionsToString()
    {
        $this->assertEquals(
            $this->optionsJson,
            (string) $this->options
        );
    }

    public function testJsonSerializationOfOptions()
    {
        $this->assertEquals(
            $this->optionsJson,
            json_encode($this->options)
        );
    }

    public function testOptionsToJson()
    {
        $this->assertEquals(
            $this->optionsJson,
            $this->options->toJson()
        );
    }

    public function testGettingOptionsViaGetMagicMethod()
    {
        $this->assertEquals('value1', $this->options->option1);
        $this->assertEquals('value2', $this->options->option2);
        $this->assertEquals('value3', $this->options->option3);
    }

    public function testSettingOptionsViaGetMagicMethod()
    {
        $this->options->option1 = 'newValue1';
        $this->options->option2 = 'newValue2';
        $this->options->option3 = 'newValue3';

        $this->assertEquals('newValue1', $this->options->option1);
        $this->assertEquals('newValue2', $this->options->option2);
        $this->assertEquals('newValue3', $this->options->option3);
    }

    public function testOptionsArrayAccess()
    {
        $this->assertEquals('value1', $this->options['option1']);

        $this->assertTrue(isset($this->options['option2']));

        $this->options['option3'] = 'newValue3';
        $this->assertEquals('newValue3', $this->options['option3']);

        unset($this->options['option2']);
        $this->assertFalse(isset($this->options['option2']));
    }

    public function testCount()
    {
        $this->assertCount(3, $this->options);
    }

    public function testTraversingWithForeach()
    {
        foreach ($this->options as $option) {
            $this->assertTrue(is_string($option));
        }
    }

    public function testHas()
    {
        $this->assertTrue($this->options->has('option1'));
        $this->assertFalse($this->options->has('option13521'));
    }

    public function testHasAndIs()
    {
        $this->assertTrue($this->options->hasAndIs('option1', 'string'));
        $this->assertFalse($this->options->hasAndIs('option1', 'array'));
    }

    public function testGet()
    {
        $this->assertEquals('value1', $this->options->get('option1'));
    }

    public function testSet()
    {
        $this->options->set('option1', 'newValue1');

        $this->assertEquals('newValue1', $this->options->get('option1'));
        $this->assertNotEquals('value1', $this->options->get('option1'));
    }

    public function testSetIfNot()
    {
        $this->options->setIfNot('option1', 'newValue1');
        $this->assertNotEquals('newValue1', $this->options->get('option1'));

        $this->options->setIfNot('newOption', 'newValue');
        $this->assertEquals('newValue', $this->options->get('newOption'));
    }

    public function testForget()
    {
        $this->options->forget('option1');

        $this->assertFalse($this->options->has('option1'));
    }

    public function testPop()
    {
        $option1 = $this->options->pop('option1');

        $this->assertEquals('value1', $option1);
        $this->assertFalse($this->options->has('option1'));
    }

    public function testMergingArrayIntoOptions()
    {
        $this->options->merge(['newOption' => 'newValue']);

        $this->assertTrue($this->options->has('newOption'));
        $this->assertEquals('newValue', $this->options->get('newOption'));
    }

    public function testMergingOptionsObjectIntoOptions()
    {
        $newOptions = new Options(['newOption' => 'newValue']);

        $this->options->merge($newOptions);

        $this->assertTrue($this->options->has('newOption'));
        $this->assertEquals('newValue', $this->options->get('newOption'));
    }

    public function testWithoutWithStringOption()
    {
        $options = $this->options->without('option2');

        $this->assertCount(2, $options);
        $this->assertArrayHasKey('option1', $options);
        $this->assertArrayNotHasKey('option2', $options);
        $this->assertArrayHasKey('option3', $options);
    }

    public function testWithoutWithArrayOfOptions()
    {
        $options = $this->options->without(['option1', 'option2']);

        $this->assertCount(1, $options);
        $this->assertArrayNotHasKey('option1', $options);
        $this->assertArrayNotHasKey('option2', $options);
        $this->assertArrayHasKey('option3', $options);
    }

    public function toArray()
    {
        $options = $this->options->toArray();

        $this->assertTrue(is_array($options));
        $this->assertArrayHasKey('option1', $options);
        $this->assertArrayHasKey('option2', $options);
        $this->assertArrayHasKey('option3', $options);
        $this->assertEquals('value1', $options['option1']);
        $this->assertEquals('value2', $options['option2']);
        $this->assertEquals('value3', $options['option3']);
    }
}
