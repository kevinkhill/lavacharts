<?php namespace Khill\Lavacharts\Tests\Configs;

use Khill\Lavacharts\Configs\Annotation;

class AnnotationTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->a = new Annotation;

        $this->mockTextStyle = $this->getMock(
            'Khill\Lavacharts\Configs\TextStyle',
            array('__construct')
        );
    }

    public function testConstructorDefaults()
    {
        $this->assertTrue($this->a->highContrast);
        $this->assertNull($this->a->textStyle);
    }

    public function testConstructorValuesAssignment()
    {
        $annotation = new Annotation(array(
            'highContrast' => false,
            'textStyle'    => $this->mockTextStyle
        ));

        $this->assertFalse($annotation->highContrast);
        $this->assertInstanceOf('Khill\Lavacharts\Configs\TextStyle', $annotation->textStyle);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testConstructorWithInvalidPropertiesKey()
    {
        $annotation = new Annotation(array('RainbowRoll' => 'spicy'));
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @dataProvider badParamsProvider1
     */
    public function testHighContrastWithBadParams($badVals)
    {
        $this->a->highContrast($badVals);
    }

    /**
     * @expectedException \PHPUnit_Framework_Error
     * @dataProvider badParamsProvider2
     */
    public function testTextStyleWithBadParams($badVals)
    {
        $this->a->textStyle($badVals);
    }


    public function badParamsProvider1()
    {
        return array(
            array('fruitsAndVeggies'),
            array(123),
            array(123.456),
            array(array()),
            array(new \stdClass()),
            array(null)
        );
    }

    public function badParamsProvider2()
    {
        return array(
            array('fruitsAndVeggies'),
            array(123),
            array(123.456),
            array(array()),
            array(new \stdClass()),
            array(true),
            array(null)
        );
    }

}

