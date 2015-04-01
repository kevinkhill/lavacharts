<?php namespace Khill\Lavacharts\Tests\Events;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Events\MouseOver;

class MouseOverTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mouseOver = new MouseOver('jsCallback');
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf('\\Khill\\Lavacharts\\Events\\MouseOver', $this->mouseOver);
    }

    public function testEventType()
    {
        $this->assertEquals('onmouseover', MouseOver::TYPE);
    }

    public function testConstructorValueAssignment()
    {
        $mouseOver = new MouseOver('jsCallback');

        $this->assertEquals('jsCallback', $this->mouseOver->callback);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testConstructorValueAssignmentWithBadType($badVals)
    {
        new MouseOver($badVals);
    }
}
