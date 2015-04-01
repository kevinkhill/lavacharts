<?php namespace Khill\Lavacharts\Tests\Configs;

use \Khill\Lavacharts\Configs\ConfigOptions;
use \Khill\Lavacharts\Configs\TextStyle;

class ConfigObjectTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testTextStyleConstructorValuesAssignment()
    {
        $textStyle = new TextStyle(array(
            'color'    => 'blue',
            'fontName' => 'Arial',
            'fontSize' => 16
        ));

        $this->assertEquals('blue', $textStyle->color);
        $this->assertEquals('Arial', $textStyle->fontName);
        $this->assertEquals(16, $textStyle->fontSize);
    }

    /**
     * @depends testTextStyleConstructorValuesAssignment
     */
    public function testToArrayWithTextStyleConfigObject()
    {
        $textStyle = new TextStyle(array(
            'color'    => 'blue',
            'fontName' => 'Arial',
            'fontSize' => 16
        ));

        $textStyleArr = $textStyle->toArray();

        $this->assertTrue(is_array($textStyleArr));
        $textStyleArrKeys = array_keys($textStyleArr);
        $this->assertEquals('textStyle', $textStyleArrKeys[0]);
        $this->assertTrue(is_array($textStyleArr['textStyle']));

        $this->assertEquals('blue', $textStyleArr['textStyle']['color']);
        $this->assertEquals('Arial', $textStyleArr['textStyle']['fontName']);
        $this->assertEquals(16, $textStyleArr['textStyle']['fontSize']);
    }

    /**
     * @depends testTextStyleConstructorValuesAssignment
     */
    public function testGetValuesWithTextStyleConfigObject()
    {
        $textStyle = new TextStyle(array(
            'color'    => 'blue',
            'fontName' => 'Arial',
            'fontSize' => 16
        ));

        $textStyleArr = $textStyle->getValues();

        $this->assertTrue(is_array($textStyleArr));

        $this->assertEquals('blue', $textStyleArr['color']);
        $this->assertEquals('Arial', $textStyleArr['fontName']);
        $this->assertEquals(16, $textStyleArr['fontSize']);
    }
}
