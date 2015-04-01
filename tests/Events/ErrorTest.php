<?php namespace Khill\Lavacharts\Tests\Events;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Events\Error;

class ErrorTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->errorEvent = new Error('jsCallback');
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf('\\Khill\\Lavacharts\\Events\\Error', $this->errorEvent);
    }

    public function testEventType()
    {
        $this->assertEquals('error', Error::TYPE);
    }

    public function testConstructorValueAssignment()
    {
        $Error = new Error('jsCallback');

        $this->assertEquals('jsCallback', $this->errorEvent->callback);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testConstructorValueAssignmentWithBadType($badVals)
    {
        new Error($badVals);
    }
}
