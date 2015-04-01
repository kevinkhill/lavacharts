<?php namespace Khill\Lavacharts\Tests\Events;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Events\AnimationFinish;

class AnimationFinishTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->animationFinishEvent = new AnimationFinish('jsCallback');
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf('\\Khill\\Lavacharts\\Events\\AnimationFinish', $this->animationFinishEvent);
    }

    public function testEventType()
    {
        $this->assertEquals('animationfinish', AnimationFinish::TYPE);
    }

    public function testConstructorValueAssignment()
    {
        $AnimationFinish = new AnimationFinish('jsCallback');

        $this->assertEquals('jsCallback', $this->animationFinishEvent->callback);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testConstructorValueAssignmentWithBadType($badVals)
    {
        new AnimationFinish($badVals);
    }
}
