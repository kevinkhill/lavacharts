<?php

namespace Khill\Lavacharts\Tests\Configs;

use \Khill\Lavacharts\Configs\UIs\CategoryUI;
use \Khill\Lavacharts\Tests\ProvidersTestCase;

class CategoryUITest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->CategoryUI = new CategoryUI;
    }

    public function testConstructorValuesAssignment()
    {
        $ui = new CategoryUI([
            'caption'              => 'Select...',
            'sortValues'           => true,
            'selectedValuesLayout' => 'aside',
            'allowNone'            => false,
            'allowMultiple'        => true,
            'allowTyping'          => false
        ]);

        $this->assertEquals($ui->caption, 'Select...');
        $this->assertTrue(  $ui->sortValues);
        $this->assertEquals($ui->selectedValuesLayout, 'aside');
        $this->assertFalse( $ui->allowNone);
        $this->assertTrue(  $ui->allowMultiple);
        $this->assertFalse( $ui->allowTyping);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testCaptionWithBadParams($badVals)
    {
        $this->CategoryUI->caption($badVals);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testSortValuesWithBadParams($badVals)
    {
        $this->CategoryUI->sortValues($badVals);
    }

    public function testSelectedValuesLayoutWithValidValues()
    {
        $values = [
            'aside',
            'below',
            'belowWrapping',
            'belowStacked'
        ];

        foreach ($values as $accepted) {
            $this->CategoryUI->selectedValuesLayout($accepted);
            $this->assertEquals($this->CategoryUI->selectedValuesLayout, $accepted);
        }
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testSelectedValuesLayoutWithInvalidOption()
    {
        $this->CategoryUI->selectedValuesLayout('inverted');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testSelectedValuesLayoutWithBadTypes($badTypes)
    {
        $this->CategoryUI->selectedValuesLayout($badTypes);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAllowNoneWithBadParams($badVals)
    {
        $this->CategoryUI->allowNone($badVals);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAllowMultipleWithBadParams($badVals)
    {
        $this->CategoryUI->allowMultiple($badVals);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAllowTypingWithBadParams($badVals)
    {
        $this->CategoryUI->allowTyping($badVals);
    }
}
