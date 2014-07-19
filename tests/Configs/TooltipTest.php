<?php namespace Khill\Lavacharts\Tests\Configs;

use Khill\Lavacharts\Configs\Tooltip;
use Khill\Lavacharts\Configs\TextStyle;

class TooltipTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->tt = new Tooltip();
    }

    public function testIfInstanceOfTooltip()
    {
        $this->assertInstanceOf('Khill\Lavacharts\Configs\Tooltip', $this->tt);
    }

    public function testConstructorDefaults()
    {
        $this->assertNull($this->tt->showColorCode);
        $this->assertNull($this->tt->textStyle);
        $this->assertNull($this->tt->trigger);
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
        $this->tt->showColorCode($badVals);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     * @dataProvider badParamsProvider
     */
    public function testTextStyleWithBadParams($badVals)
    {
        $this->tt->textStyle($badVals);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @dataProvider badParamsProvider
     */
    public function testTriggerWithBadParams($badVals)
    {
        $this->tt->trigger($badVals);
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

