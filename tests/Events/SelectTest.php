<?php namespace Khill\Lavacharts\Tests\Events;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Events\Select;

class SelectTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->selectEvent = new Select('jsCallback');
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf('\\Khill\\Lavacharts\\Events\\Select', $this->selectEvent);
    }

    public function testEventType()
    {
        $this->assertEquals('select', Select::TYPE);
    }

    public function testConstructorValueAssignment()
    {
        $Select = new Select('jsCallback');

        $this->assertEquals('jsCallback', $this->selectEvent->callback);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testConstructorValueAssignmentWithBadType($badVals)
    {
        new Select($badVals);
    }
}
