<?php

namespace Khill\Lavacharts\Tests\Configs;

use \Mockery as m;
use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Configs\UIs\NumberRangeUI;

class NumberRangeUITest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->NumberRangeUI = new NumberRangeUI;
    }

    public function testConstructorDefaults()
    {
        //$this->assertNull($this->a->alwaysOutside);
        //$this->assertNull($this->a->highContrast);
        //$this->assertNull($this->a->textStyle);
    }

    public function testConstructorValuesAssignment()
    {
        $ui = new NumberRangeUI([
            'label'          => 'Tacos',
            'labelSeparator' => ':',
            'labelStacking'  => 'horizontal',
            'cssClass'       => 'fancy'
        ]);

        $this->assertEquals($ui->label, 'Tacos');
        $this->assertEquals($ui->labelSeparator, ':');
        $this->assertEquals($ui->labelStacking, 'horizontal');
        $this->assertEquals($ui->cssClass, 'fancy');
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testConstructorWithInvalidPropertiesKey()
    {
        new NumberRangeUI(['Pickles' => 'tasty']);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     *
    public function testAlwaysOutsideWithBadParams($badVals)
    {
        $this->a->alwaysOutside($badVals);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     *
    public function testHighContrastWithBadParams($badVals)
    {
        $this->a->highContrast($badVals);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     *
    public function testTextStyleWithNonTextStyle()
    {
        $this->a->textStyle('This is not a TextStyle Object');
    }*/
}
