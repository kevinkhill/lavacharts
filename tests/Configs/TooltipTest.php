<?php namespace Khill\Lavacharts\Tests\Configs;

use \Khill\Lavacharts\Configs\Tooltip;

class TooltipTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->tt = new Tooltip();

        $this->mockTextStyle = $this->getMock(
            '\Khill\Lavacharts\Configs\TextStyle',
            ['__construct']
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
        $tooltip = new Tooltip([
            'showColorCode' => true,
            'textStyle'     => $this->mockTextStyle,
            'trigger'       => 'focus'
        ]);

        $this->assertTrue($tooltip->showColorCode);
        $this->assertTrue(is_array($tooltip->textStyle));
        $this->assertEquals('focus', $tooltip->trigger);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testConstructorWithInvalidPropertiesKey()
    {
        new Tooltip(['Fruits' => []]);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
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
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @dataProvider badParamsProvider
     */
    public function testTriggerWithBadParams($badVals)
    {
        $this->tt->trigger($badVals);
    }


    public function badParamsProvider()
    {
        return [
            ['stringy'],
            [123],
            [123.456],
            [[]],
            [new \stdClass()],
            [null]
        ];
    }
}
