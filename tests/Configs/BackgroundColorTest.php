<?php namespace Khill\Lavacharts\Tests\Configs;

use \Khill\Lavacharts\Configs\BackgroundColor;

class BackgroundColorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->bgc = new BackgroundColor;
    }

    public function testConstructorDefaults()
    {
        $this->assertNull($this->bgc->stroke);
        $this->assertNull($this->bgc->strokeWidth);
    }

    public function testConstructorValuesAssignment()
    {
        $backgroundColor = new BackgroundColor(array(
            'stroke'      => '#E3D5F2',
            'strokeWidth' => 6,
            'fill'        => '#B0C9E3'
        ));

        $this->assertEquals('#E3D5F2', $backgroundColor->stroke);
        $this->assertEquals(6, $backgroundColor->strokeWidth);
        $this->assertEquals('#B0C9E3', $backgroundColor->fill);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testConstructorWithInvalidPropertiesKey()
    {
        $bgc = new BackgroundColor(array('TunaSalad' => 'sandwich'));
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @dataProvider badParamsProvider1
     */
    public function testStrokeWithBadParams($badVals)
    {
        $this->bgc->stroke($badVals);
    }

    /**
     * /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @dataProvider badParamsProvider2
     */
    public function testStrokeWidthWithBadParams($badVals)
    {
        $this->bgc->strokeWidth($badVals);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @dataProvider badParamsProvider1
     */
    public function testFillWithBadParams($badVals)
    {
        $this->bgc->fill($badVals);
    }


    public function badParamsProvider1()
    {
        return array(
            array(123),
            array(123.456),
            array(array()),
            array(new \stdClass),
            array(true),
            array(null)
        );
    }

    public function badParamsProvider2()
    {
        return array(
            array('fruitsAndVeggies'),
            array(123.456),
            array(array()),
            array(new \stdClass),
            array(true),
            array(null)
        );
    }
}
