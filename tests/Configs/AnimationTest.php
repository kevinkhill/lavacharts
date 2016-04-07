<?php

namespace Khill\Lavacharts\Tests\Configs;

use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\Configs\Animation;

class AnimationTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->Animation = new Animation;
    }

    public function testConstructorValuesAssignment()
    {
        $animation = new Animation([
            'duration' => 300,
            'easing'   => 'linear',
            'startup'  => true
        ]);

        $this->assertEquals(300, $animation->duration);
        $this->assertEquals('linear', $animation->easing);
        $this->assertTrue($animation->startup);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testConstructorWithInvalidPropertiesKey()
    {
        new Animation(['Pickles' => 'tasty']);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDurationWithBadParams($badParams)
    {
        $this->Animation->duration($badParams);
    }

    /**
     * @dataProvider easingVals
     */
    public function testEasingWithAcceptedValues($easingVals)
    {
        $this->Animation->easing($easingVals);
        $this->assertEquals($easingVals, $this->Animation->easing);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testEasingWithNonAcceptableValue()
    {
        $this->Animation->easing('fast');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testEasingWithBadParams($badParams)
    {
        $this->Animation->easing($badParams);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testStartupWithBadParams($badParams)
    {
        $this->Animation->startup($badParams);
    }

    public function easingVals()
    {
        return [
            ['linear'],
            ['in'],
            ['out'],
            ['inAndOut'],
        ];
    }
}
