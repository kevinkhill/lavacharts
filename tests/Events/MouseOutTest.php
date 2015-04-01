<?php namespace Khill\Lavacharts\Tests\Events;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Events\MouseOut;

class MouseOutTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mouseOut = new MouseOut('jsCallback');
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf('\\Khill\\Lavacharts\\Events\\MouseOut', $this->mouseOut);
    }

    public function testEventType()
    {
        $this->assertEquals('onmouseout', MouseOut::TYPE);
    }

    public function testConstructorValueAssignment()
    {
        $mouseOut = new MouseOut('jsCallback');

        $this->assertEquals('jsCallback', $this->mouseOut->callback);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testConstructorValueAssignmentWithBadType($badVals)
    {
        new MouseOut($badVals);
    }
}
