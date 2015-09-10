<?php

namespace Khill\Lavacharts\Tests\Configs\UIs;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Configs\UIs\StringUI;

class StringUITest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->StringUI = new StringUI;
    }

    public function testConstructorValuesAssignment()
    {
        $ui = new StringUI([
            'realtimeTrigger' => true,
        ]);

        $this->assertEquals($ui->realtimeTrigger, 1);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testConstructorWithInvalidPropertiesKey()
    {
        new StringUI(['Veggies' => 'broccoli']);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testRealtimeTriggerWithBadParams($badVals)
    {
        $this->StringUI->realtimeTrigger($badVals);
    }
}
