<?php namespace Khill\Lavacharts\Tests\Configs;

use Khill\Lavacharts\Lavacharts;
use Khill\Lavacharts\Tests\TestCase;
use Khill\Lavacharts\Configs\annotation;
use Khill\Lavacharts\Configs\textStyle;

class AnnotationTest extends TestCase {

    public function setUp()
    {
        parent::setUp();

        $this->a = new annotation();
    }

    public function testIfInstanceOfannotation()
    {
        $this->assertInstanceOf('Khill\Lavacharts\Configs\annotation', $this->a);
    }

    public function testConstructorDefaults()
    {
        $this->assertTrue($this->a->highContrast);
        $this->assertEquals(null, $this->a->textStyle);
    }

    public function testConstructorValuesAssignment()
    {
        $annotation = new annotation(array(
            'highContrast' => false,
            'textStyle'    => new textStyle()
        ));

        $this->assertFalse($annotation->highContrast);
        $this->assertInstanceOf('Khill\Lavacharts\Configs\textStyle', $annotation->textStyle);
    }

    public function testConstructorWithInvalidPropertiesKey()
    {
        $annotation = new annotation(array('RainbowRoll' => 'spicy'));

        $this->assertTrue(Lavacharts::hasErrors());
    }

    /**
     * @dataProvider badParamsProvider1
     */
    public function testHighContrastWithBadParams($badVals)
    {
        $this->a->highContrast($badVals);

        $this->assertTrue(Lavacharts::hasErrors());
    }

    /**
     * @dataProvider badParamsProvider2
     */
    public function testTextStyleWithBadParams($badVals)
    {
        $this->a->textStyle($badVals);

        $this->assertTrue(Lavacharts::hasErrors());
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

