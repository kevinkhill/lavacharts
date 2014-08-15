<?php namespace Lavacharts\Tests\Configs;

use \Lavacharts\Configs\Tooltip;

class TooltipTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->tt = new Tooltip();

        $this->mockTextStyle = $this->getMock(
            '\Lavacharts\Configs\TextStyle',
            array('__construct')
        );
    }

    public function testConstructorDefaults()
    {
        $this->assertNull($this->tt->showColorCode);
        $this->assertNull($this->tt->textStyle);
        $this->assertNull($this->tt->trigger);
    }

    public function testConstructorValuesAssignment()
    {
        $mockTextStyle = $this->getMock(
            '\Lavacharts\Configs\TextStyle',
            array('__construct')
        );

        $tooltip = new Tooltip(array(
            'showColorCode' => true,
            'textStyle'     => $this->mockTextStyle,
            'trigger'       => 'focus'
        ));

        $this->assertTrue($tooltip->showColorCode);
        $this->assertTrue(is_array($tooltip->textStyle));
        $this->assertEquals('focus', $tooltip->trigger);
    }

    /**
     * @expectedException Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testConstructorWithInvalidPropertiesKey()
    {
        new Tooltip(array('Fruits' => array()));
    }

    /**
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
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
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
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

