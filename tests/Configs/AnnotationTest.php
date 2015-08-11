<?php

namespace Khill\Lavacharts\Tests\Configs;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Configs\Annotation;
use \Mockery as m;

class AnnotationTest extends ProvidersTestCase
{
    public $Annotation;

    public function setUp()
    {
        parent::setUp();

        $this->Annotation = new Annotation;
    }

    public function testConstructorValuesAssignment()
    {
        $annotation = new Annotation([
            'alwaysOutside' => true,
            'highContrast'  => false,
            'textStyle'     => [
                'fontName' => 'Arial',
                'fontSize' => 20
            ]
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
        $this->Annotation->alwaysOutside($badVals);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testHighContrastWithBadParams($badVals)
    {
        $this->Annotation->highContrast($badVals);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTextStyleWithBadParams($badVals)
    {
        $this->Annotation->textStyle($badVals);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testTextStyleWithBadArrayConfig()
    {
        $this->Annotation->textStyle(['NotAReal'=>'ConfigOption']);
    }
}
