<?php namespace Khill\Lavacharts\Tests\Configs;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Configs\Annotation;
use \Mockery as m;

class AnnotationTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->a = new Annotation;

        $this->mockTextStyle = $this->getMock(
            '\Khill\Lavacharts\Configs\TextStyle',
            ['__construct']
        );
    }

    public function testConstructorDefaults()
    {
        $this->assertNull($this->a->alwaysOutside);
        $this->assertNull($this->a->highContrast);
        $this->assertNull($this->a->textStyle);
    }

    public function testConstructorValuesAssignment()
    {
        $annotation = new Annotation([
            'alwaysOutside' => true,
            'highContrast'  => false,
            'textStyle'     => $this->mockTextStyle
        ]);

        $this->assertTrue($annotation->alwaysOutside);
        $this->assertFalse($annotation->highContrast);
        $this->assertInstanceOf('\Khill\Lavacharts\Configs\TextStyle', $annotation->textStyle);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testConstructorWithInvalidPropertiesKey()
    {
        new Annotation(['RainbowRoll' => 'spicy']);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAlwaysOutsideWithBadParams($badVals)
    {
        $this->a->alwaysOutside($badVals);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testHighContrastWithBadParams($badVals)
    {
        $this->a->highContrast($badVals);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testTextStyleWithNonTextStyle()
    {
        $this->a->textStyle('This is not a TextStyle Object');
    }
}
