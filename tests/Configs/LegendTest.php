<?php namespace Lavacharts\Tests\Configs;

use \Lavacharts\Tests\ProvidersTestCase;
use \Lavacharts\Configs\Legend;
use \Mockery as m;

class LegendTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->l = new Legend();

        $this->mockTextStyle = $this->getMock(
            '\Lavacharts\Configs\TextStyle',
            array('__construct')
        );
    }

    public function testPositionWithValidValues()
    {
        $this->l->position('right');
        $this->assertEquals('right', $this->l->position);

        $this->l->position('top');
        $this->assertEquals('top', $this->l->position);

        $this->l->position('bottom');
        $this->assertEquals('bottom', $this->l->position);

        $this->l->position('in');
        $this->assertEquals('in', $this->l->position);

        $this->l->position('none');
        $this->assertEquals('none', $this->l->position);
    }

    /**
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testPositionWithBadValue()
    {
        $this->l->position('underneath');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testPositionWithBadTypes($badTypes)
    {
        $this->l->position($badTypes);
    }

    public function testAlignmentWithValidValues()
    {
        $this->l->alignment('start');
        $this->assertEquals('start', $this->l->alignment);

        $this->l->alignment('center');
        $this->assertEquals('center', $this->l->alignment);

        $this->l->alignment('end');
        $this->assertEquals('end', $this->l->alignment);
    }

    /**
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAlignmentWithBadValue()
    {
        $this->l->alignment('yesterday');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAlignmentWithBadTypes($badTypes)
    {
        $this->l->alignment($badTypes);
    }

    public function testTextStyleWithValidConfigObject()
    {/*
        $mockTextStyle = $this->getMock(
            '\Lavacharts\Configs\TextStyle',
            array('__construct')
        );*/
        $textStyleVals = array(
            'color'    => 'blue',
            'fontName' => 'Arial',
            'fontSize' => 16
        );

        $mockTextStyle = m::mock('Lavacharts\Configs\TextStyle');
        $mockTextStyle->shouldReceive('getValues')->once()->andReturn($textStyleVals);

        $this->l->textStyle($mockTextStyle);

        $this->assertEquals($textStyleVals, $this->l->textStyle);
    }
}

