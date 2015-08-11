<?php

namespace Khill\Lavacharts\Tests\Configs;

use \Khill\Lavacharts\Configs\BoxStyle;
use \Mockery as m;

class BoxStyleTest extends \PHPUnit_Framework_TestCase //TODO fix this to use providerclass
{
    public $BoxStyle;

    public function setUp()
    {
        parent::setUp();

        $this->BoxStyle = new BoxStyle;
    }

    public function testConstructorValuesAssignment()
    {
        $boxStyle = new BoxStyle([
            'stroke'      => '#5B5B5B',
            'strokeWidth' => '5',
            'rx'          => '10',
            'ry'          => '10',
            'gradient'    => []
        ]);

        $this->assertEquals('#5B5B5B', $boxStyle->stroke);
        $this->assertEquals('5', $boxStyle->strokeWidth);
        $this->assertEquals('10', $boxStyle->rx);
        $this->assertEquals('10', $boxStyle->ry);
        $this->assertInstanceOf('\Khill\Lavacharts\Configs\Gradient', $boxStyle->gradient);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testConstructorWithInvalidPropertiesKey()
    {
        new BoxStyle(['Lasagna' => '50%']);
    }

    public function testStokeWithNumericParams()
    {
        $this->BoxStyle->stroke('#DE02FB');

        $this->assertEquals('#DE02FB', $this->BoxStyle->stroke);
    }

    /**
     * @dataProvider numericParamsProvider
     */
    public function testStokeWidthWithNumericParams($testNum)
    {
        $this->BoxStyle->strokeWidth($testNum);

        $this->assertEquals($testNum, $this->BoxStyle->strokeWidth);
    }

    /**
     * @dataProvider numericParamsProvider
     */
    public function testRxWithNumericParams($testNum)
    {
        $this->BoxStyle->rx($testNum);

        $this->assertEquals($testNum, $this->BoxStyle->rx);
    }

    /**
     * @dataProvider numericParamsProvider
     */
    public function testRyWithNumericParams($testNum)
    {
        $this->BoxStyle->ry($testNum);

        $this->assertEquals($testNum, $this->BoxStyle->ry);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @dataProvider badParamsProvider
     */
    public function testStrokeWithBadParams($badVals)
    {
        $this->BoxStyle->stroke($badVals);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @dataProvider badParamsProvider
     */
    public function testStokeWidthWithBadParams($badVals)
    {
        $this->BoxStyle->strokeWidth($badVals);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @dataProvider badParamsProvider
     */
    public function testRxWithBadParams($badVals)
    {
        $this->BoxStyle->rx($badVals);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @dataProvider badParamsProvider
     */
    public function testRyWithBadParams($badVals)
    {
        $this->BoxStyle->ry($badVals);
    }


    public function badParamsProvider()
    {
        return [
            [[]],
            [new \stdClass],
            [true],
            [null]
        ];
    }

    public function numericParamsProvider()
    {
        return [
            [123],
            ['123']
        ];
    }
}
