<?php

namespace Khill\Lavacharts\Tests\Configs;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Configs\Legend;

class LegendTest extends ProvidersTestCase
{
    public $Legend;

    public function setUp()
    {
        parent::setUp();

        $this->Legend = new Legend();
    }

    public function testPositionWithValidValues()
    {
        $this->Legend->position('right');
        $this->assertEquals('right', $this->Legend->position);

        $this->Legend->position('top');
        $this->assertEquals('top', $this->Legend->position);

        $this->Legend->position('bottom');
        $this->assertEquals('bottom', $this->Legend->position);

        $this->Legend->position('in');
        $this->assertEquals('in', $this->Legend->position);

        $this->Legend->position('none');
        $this->assertEquals('none', $this->Legend->position);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testPositionWithBadValue()
    {
        $this->Legend->position('underneath');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testPositionWithBadTypes($badTypes)
    {
        $this->Legend->position($badTypes);
    }

    public function testAlignmentWithValidValues()
    {
        $this->Legend->alignment('start');
        $this->assertEquals('start', $this->Legend->alignment);

        $this->Legend->alignment('center');
        $this->assertEquals('center', $this->Legend->alignment);

        $this->Legend->alignment('end');
        $this->assertEquals('end', $this->Legend->alignment);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAlignmentWithBadValue()
    {
        $this->Legend->alignment('yesterday');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAlignmentWithBadTypes($badTypes)
    {
        $this->Legend->alignment($badTypes);
    }

    public function testTextStyleWithValidConfigObject()
    {
        $this->Legend->textStyle([
            'fontSize' => 20
        ]);

        $this->assertInstanceOf('Khill\Lavacharts\Configs\TextStyle', $this->Legend->textStyle);
    }
}
