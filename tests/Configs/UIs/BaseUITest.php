<?php

namespace Khill\Lavacharts\Tests\Configs\UIs;

use \Khill\Lavacharts\Tests\ProvidersTestCase;

class BaseUITest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->MockUI = new MockUI();
    }

    public function testConstructorValuesAssignment()
    {
        $ui = new MockUI([
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

    public function testGetType()
    {
        $this->assertEquals('MockUI', $this->MockUI->getType());
    }

    public function testLabel()
    {
        $this->MockUI->label('Carrots');

        $this->assertEquals($this->MockUI->label, 'Carrots');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testLabelWithBadParams($badVals)
    {
        $this->MockUI->label($badVals);
    }

    public function testLabelSeparator()
    {
        $this->MockUI->labelSeparator('|');

        $this->assertEquals($this->MockUI->labelSeparator, '|');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testLabelSeparatorWithBadParams($badVals)
    {
        $this->MockUI->labelSeparator($badVals);
    }

    public function testLabelStackingWithValidValues()
    {
        $this->MockUI->labelStacking('horizontal');
        $this->assertEquals($this->MockUI->labelStacking, 'horizontal');

        $this->MockUI->labelStacking('vertical');
        $this->assertEquals($this->MockUI->labelStacking, 'vertical');
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testLabelStackingWithInvalidOption()
    {
        $this->MockUI->labelStacking('upsidedown');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testLabelStackingWithBadTypes($badTypes)
    {
        $this->MockUI->labelStacking($badTypes);
    }

    public function testCssClass()
    {
        $this->MockUI->cssClass('fancy-class');
        $this->assertEquals($this->MockUI->cssClass, 'fancy-class');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testCssClassWithBadParams($badVals)
    {
        $this->MockUI->cssClass($badVals);
    }

    public function testGetOptions()
    {
        $this->assertInstanceOf('\Khill\Lavacharts\Options', $this->MockUI->getOptions());
    }

    public function testJsonSerialization()
    {
        $ui = new MockUI([
            'label'          => 'Tacos',
            'labelSeparator' => '|',
            'labelStacking'  => 'horizontal',
            'cssClass'       => 'fancy'
        ]);

        $jsonSerialization = '{"label":"Tacos","labelSeparator":"|","labelStacking":"horizontal","cssClass":"fancy"}';

        $this->assertEquals(json_encode($ui), $jsonSerialization);
    }
}
