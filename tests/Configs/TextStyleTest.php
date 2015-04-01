<?php namespace Khill\Lavacharts\Tests\Configs;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Configs\TextStyle;

class TextStyleTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->ts = new TextStyle;
    }

    public function testConstructorValuesAssignment()
    {
        $textStyle = new TextStyle(array(
            'color'    => 'blue',
            'fontName' => 'Arial',
            'fontSize' => 16,
            'bold'     => true,
            'italic'   => true
        ));

        $this->assertEquals('blue', $textStyle->color);
        $this->assertEquals('Arial', $textStyle->fontName);
        $this->assertEquals(16, $textStyle->fontSize);
        $this->assertTrue($textStyle->bold);
        $this->assertTrue($textStyle->italic);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testConstructorWithInvalidPropertiesKey()
    {
        new TextStyle(array('Burrito' => array(123)));
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testBoldWithBadParams($badParams)
    {
        $this->ts->bold($badParams);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testColorWithBadParams($badParams)
    {
        $this->ts->color($badParams);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testFontNameWithBadParams($badParams)
    {
        $this->ts->fontName($badParams);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testFontSizeWithBadParams($badParams)
    {
        $this->ts->fontSize($badParams);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testItalicWithBadParams($badParams)
    {
        $this->ts->italic($badParams);
    }
}
