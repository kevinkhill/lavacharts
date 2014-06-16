<?php namespace Khill\Lavacharts\Tests\Configs;

use Khill\Lavacharts\Configs\Tooltip;
use Khill\Lavacharts\Configs\TextStyle;

class TooltipTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->t = new Tooltip();
    }

    public function testIfInstanceOfTooltip()
    {
        $this->assertInstanceOf('Khill\Lavacharts\Configs\Tooltip', $this->t);
    }

    public function testConstructorDefaults()
    {
        $this->assertNull($this->t->showColorCode);
        $this->assertNull($this->t->textStyle);
        $this->assertNull($this->t->trigger);
    }

    public function testConstructorValuesAssignment()
    {
        $tooltip = new Tooltip(array(
            'showColorCode' => true,
            'textStyle'     => new TextStyle(),
            'trigger'       => 'focus'
        ));

        $this->assertTrue($tooltip->showColorCode);
        $this->assertEquals(array(), $tooltip->textStyle);
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
        $this->t->showColorCode($badVals);
    }

    /**
     * @expectedException \PHPUnit_Framework_Error
     * @dataProvider badParamsProvider
     */
    public function testTextStyleWithBadParams($badVals)
    {
        $this->t->textStyle($badVals);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @dataProvider badParamsProvider
     */
    public function testTriggerWithBadParams($badVals)
    {
        $this->t->trigger($badVals);
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

