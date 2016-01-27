<?php

namespace Khill\Lavacharts\Tests\Configs;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Configs\TextStyle;

class TextStyleTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->TextStyle = new TextStyle;
    }

    public function testConstructorValuesAssignment()
    {
        $textStyle = new TextStyle([
            'color'    => 'blue',
            'fontName' => 'Arial',
            'fontSize' => 16,
            'bold'     => true,
            'italic'   => true
        ]);

        $this->assertEquals('blue', $textStyle->color);
        $this->assertEquals('Arial', $textStyle->fontName);
        $this->assertEquals(16, $textStyle->fontSize);
        $this->assertTrue($textStyle->bold);
        $this->assertTrue($textStyle->italic);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testConstructorWithInvalidPropertiesKey()
    {
        new TextStyle(['Burrito' => [123]]);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testBoldWithBadParams($badParams)
    {
        $this->TextStyle->bold($badParams);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testColorWithBadParams($badParams)
    {
        $this->TextStyle->color($badParams);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testFontNameWithBadParams($badParams)
    {
        $this->TextStyle->fontName($badParams);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testFontSizeWithBadParams($badParams)
    {
        $this->TextStyle->fontSize($badParams);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testItalicWithBadParams($badParams)
    {
        $this->TextStyle->italic($badParams);
    }

    public function testJsonSerialization()
    {
        $textStyle = new TextStyle([
            'color'    => 'red',
            'fontName' => 'Arial',
            'fontSize' => 12,
            'italic'   => true
        ]);

        $jsonSerialization = '{"color":"red","fontName":"Arial","fontSize":12,"italic":true}';

        $this->assertEquals(json_encode($textStyle), $jsonSerialization);
    }
}
