<?php

namespace Khill\Lavacharts\Tests\Configs\UIs;

use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\Configs\UIs\NumberRangeUI;

class NumberRangeUITest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->NumberRangeUI = new NumberRangeUI;
    }

    public function testConstructorValuesAssignment()
    {
        $ui = new NumberRangeUI([
            'format'          => [
                'decimalSymbol' => '.'
            ],
            'step'            => 1,
            'ticks'           => 2,
            'unitIncrement'   => 5,
            'blockIncrement'  => 10,
            'showRangeValues' => true,
            'orientation'     => 'vertical'
        ]);

        $this->assertInstanceOf('Khill\Lavacharts\DataTables\Formats\NumberFormat', $ui->format);
        $this->assertEquals($ui->step, 1);
        $this->assertEquals($ui->ticks, 2);
        $this->assertEquals($ui->unitIncrement, 5);
        $this->assertEquals($ui->blockIncrement, 10);
        $this->assertTrue(  $ui->showRangeValues);
        $this->assertEquals($ui->orientation, 'vertical');
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testConstructorWithInvalidPropertiesKey()
    {
        new NumberRangeUI(['Pickles' => 'tasty']);
    }

    /**
     * @dataProvider nonNumericProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testStepWithBadParams($badVals)
    {
        $this->NumberRangeUI->step($badVals);
    }

    /**
     * @dataProvider nonNumericProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTicksWithBadParams($badVals)
    {
        $this->NumberRangeUI->ticks($badVals);
    }

    /**
     * @dataProvider nonNumericProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testUnitIncrementWithBadParams($badVals)
    {
        $this->NumberRangeUI->unitIncrement($badVals);
    }

    /**
     * @dataProvider nonNumericProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testBlockIncrementWithBadParams($badVals)
    {
        $this->NumberRangeUI->blockIncrement($badVals);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testShowRangeValuesWithBadParams($badVals)
    {
        $this->NumberRangeUI->showRangeValues($badVals);
    }
}
