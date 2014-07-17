<?php namespace Khill\Lavacharts\Tests\Configs;

use Khill\Lavacharts\Configs\Tooltip;
use Khill\Lavacharts\Configs\TextStyle;

class TooltipTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->tooltip = new Tooltip();
    }

    public function testIfInstanceOfTooltip()
    {
        $this->assertInstanceOf('Khill\Lavacharts\Configs\Tooltip', $this->tooltip);
    }

    public function testConstructorDefaults()
    {
        $this->assertNull($this->tooltip->showColorCode);
        $this->assertNull($this->tooltip->textStyle);
        $this->assertNull($this->tooltip->trigger);
    }

    public function testConstructorValuesAssignment()
    {
        $tooltip = new Tooltip(array(
            'showColorCode' => true,
            'textStyle'     => new TextStyle(),
            'trigger'       => 'focus'
        ));

        $this->assertTrue($tooltip->showColorCode);
        $this->assertTrue(is_array($tooltip->textStyle));
        $this->assertEquals('focus', $tooltip->trigger);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testConstructorWithInvalidPropertiesKey()
    {
        new Tooltip(array('Fruits' => array()));
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @dataProvider badParamsProvider
     */
    public function testShowColorCodeWithBadParams($badVals)
    {
        $this->tooltip->showColorCode($badVals);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @dataProvider badParamsProvider
     */
    public function testTextStyleWithBadParams($badVals)
    {
        $this->tooltip->textStyle($badVals);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @dataProvider badParamsProvider
     */
    public function testTriggerWithBadParams($badVals)
    {
        $this->tooltip->trigger($badVals);
    }


    public function badParamsProvider()
    {
        return array(
            array('stringy'),
            array(123),
            array(123.456),
            array(array()),
            array(new \stdClass()),
            array(null)
        );
    }

}

