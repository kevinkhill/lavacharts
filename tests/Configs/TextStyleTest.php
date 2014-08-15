<?php namespace Lavacharts\Tests\Configs;

use \Lavacharts\Configs\TextStyle;

class TextStyleTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->ts = new TextStyle;
    }

    public function testConstructorDefaults()
    {
        $this->assertNull($this->ts->color);
        $this->assertNull($this->ts->fontName);
        $this->assertNull($this->ts->fontSize);
    }

    public function testConstructorValuesAssignment()
    {
        $textStyle = new TextStyle(array(
            'color'    => 'blue',
            'fontName' => 'Arial',
            'fontSize' => 16
        ));

        $this->assertEquals('blue',  $textStyle->color);
        $this->assertEquals('Arial', $textStyle->fontName);
        $this->assertEquals(16,      $textStyle->fontSize);
    }

    /**
     * @expectedException Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testConstructorWithInvalidPropertiesKey()
    {
        new TextStyle(array('Burrito' => array(123)));
    }

    /**
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     * @dataProvider badParamsProvider
     */
    public function testColorWithBadParams($badVals)
    {
        $this->ts->color($badVals);
    }

    /**
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     * @dataProvider badParamsProvider
     */
    public function testFontNameWithBadParams($badVals)
    {
        $this->ts->fontName($badVals);
    }

    /**
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     * @dataProvider badParamsProvider
     */
    public function testFontSizeWithBadParams($badVals)
    {
        $this->ts->fontSize($badVals);
    }


    public function badParamsProvider()
    {
        return array(
            array(true),
            array(123.456),
            array(array()),
            array(new \stdClass()),
            array(null)
        );
    }

}

