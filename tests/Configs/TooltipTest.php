<?php

namespace Khill\Lavacharts\Tests\Configs;

use \Khill\Lavacharts\Configs\Tooltip;
use \Khill\Lavacharts\Tests\ProvidersTestCase;

class TooltipTest extends ProvidersTestCase
{
    public $Tooltip;

    public function setUp()
    {
        parent::setUp();

        $this->Tooltip = new Tooltip();
    }

    public function testConstructorValuesAssignment()
    {
        $tooltip = new Tooltip([
            'showColorCode' => true,
            'textStyle'     => [
                'fontSize' => 12,
                'bold' => true
            ],
            'trigger' => 'focus'
        ]);

        $this->assertTrue($tooltip->showColorCode);
        $this->assertInstanceOf('Khill\Lavacharts\Configs\TextStyle', $tooltip->textStyle);
        $this->assertEquals('focus', $tooltip->trigger);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testConstructorWithInvalidPropertiesKey()
    {
        new Tooltip(['Fruits' => 3]);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testShowColorCodeWithBadParams($badVals)
    {
        $this->Tooltip->showColorCode($badVals);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTextStyleWithBadParams($badVals)
    {
        $this->Tooltip->textStyle($badVals);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTriggerWithInvalidOption()
    {
        $this->Tooltip->trigger('Apples');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTriggerWithBadParams($badVals)
    {
        $this->Tooltip->trigger($badVals);
    }
}
