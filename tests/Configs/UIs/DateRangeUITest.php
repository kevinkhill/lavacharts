<?php

namespace Khill\Lavacharts\Tests\Configs;

use \Mockery as m;
use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Configs\UIs\DateRangeUI;

class DateRangeUITest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->DateRangeUI = new DateRangeUI;
    }

    public function testConstructorValuesAssignment()
    {
        $mockDateFormat = m::mock('\Khill\Lavacharts\DataTables\Formats\DateFormat');

        $ui = new DateRangeUI([
            'format'          => $mockDateFormat,
            'step'            => 1,
            'ticks'           => 2,
            'unitIncrement'   => 5,
            'blockIncrement'  => 10,
            'showRangeValues' => true,
            'orientation'     => 'vertical'
        ]);

        $this->assertEquals($ui->step, 1);
        $this->assertEquals($ui->ticks, 2);
        $this->assertEquals($ui->unitIncrement, 5);
        $this->assertEquals($ui->blockIncrement, 10);
        $this->assertTrue(  $ui->showRangeValues);
        $this->assertEquals($ui->orientation, 'vertical');
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidUIProperty
     */
    public function testConstructorWithInvalidPropertiesKey()
    {
        new DateRangeUI(['Chrome' => 'metal']);
    }

    /**
     * @dataProvider nonNumericProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testStepWithBadParams($badVals)
    {
        $this->DateRangeUI->step($badVals);
    }

    /**
     * @dataProvider nonNumericProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTicksWithBadParams($badVals)
    {
        $this->DateRangeUI->ticks($badVals);
    }

    /**
     * @dataProvider nonNumericProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testUnitIncrementWithBadParams($badVals)
    {
        $this->DateRangeUI->unitIncrement($badVals);
    }

    /**
     * @dataProvider nonNumericProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testBlockIncrementWithBadParams($badVals)
    {
        $this->DateRangeUI->blockIncrement($badVals);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testShowRangeValuesWithBadParams($badVals)
    {
        $this->DateRangeUI->showRangeValues($badVals);
    }
}
