<?php namespace Khill\Lavacharts\Tests\Events;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Events\Ready;

class ReadyTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->readyEvent = new Ready('jsCallback');
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf('\\Khill\\Lavacharts\\Events\\Ready', $this->readyEvent);
    }

    public function testEventType()
    {
        $this->assertEquals('ready', Ready::TYPE);
    }

    public function testConstructorValueAssignment()
    {
        $Ready = new Ready('jsCallback');

        $this->assertEquals('jsCallback', $this->readyEvent->callback);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testConstructorValueAssignmentWithBadType($badVals)
    {
        new Ready($badVals);
    }
}
