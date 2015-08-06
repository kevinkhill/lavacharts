<?php

namespace Khill\Lavacharts\Tests\Configs;

use \Mockery as m;
use \Khill\Lavacharts\Configs\UIs\UI;
use \Khill\Lavacharts\Tests\ProvidersTestCase;

class BaseUITest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockOptions = m::mock('\Khill\Lavacharts\Configs\Options', [[
            'label',
            'labelSeparator',
            'labelStacking',
            'cssClass'
        ]])->makePartial();

        $this->UI = new UI($this->mockOptions);
    }

    public function testConstructorValuesAssignment()
    {
        $ui = new UI($this->mockOptions, [
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

    public function testLabel()
    {
        $this->UI->label('Carrots');

        $this->assertEquals($this->UI->label, 'Carrots');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testLabelWithBadParams($badVals)
    {
        $this->UI->label($badVals);
    }

    public function testLabelSeparator()
    {
        $this->UI->labelSeparator('|');

        $this->assertEquals($this->UI->labelSeparator, '|');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testLabelSeparatorWithBadParams($badVals)
    {
        $this->UI->labelSeparator($badVals);
    }

    public function testLabelStackingWithValidValues()
    {
        $this->UI->labelStacking('horizontal');
        $this->assertEquals($this->UI->labelStacking, 'horizontal');

        $this->UI->labelStacking('vertical');
        $this->assertEquals($this->UI->labelStacking, 'vertical');
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testLabelStackingWithInvalidOption()
    {
        $this->UI->labelStacking('upsidedown');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testLabelStackingWithBadTypes($badTypes)
    {
        $this->UI->labelStacking($badTypes);
    }

    public function testCssClass()
    {
        $this->UI->cssClass('fancy-class');
        $this->assertEquals($this->UI->cssClass, 'fancy-class');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testCssClassWithBadParams($badVals)
    {
        $this->UI->cssClass($badVals);
    }

    public function testGetOptions()
    {
        $this->assertInstanceOf('\Khill\Lavacharts\Configs\Options', $this->UI->getOptions());
    }

    public function testJsonSerialization()
    {
        $ui = new UI($this->mockOptions, [
            'label'          => 'Tacos',
            'labelSeparator' => '|',
            'labelStacking'  => 'horizontal',
            'cssClass'       => 'fancy'
        ]);

        $jsonSerialization = '{"label":"Tacos","labelSeparator":"|","labelStacking":"horizontal","cssClass":"fancy"}';

        $this->assertEquals(json_encode($ui), $jsonSerialization);
    }
}
