<?php namespace Khill\Lavacharts\Tests\Configs;

use \Khill\Lavacharts\Configs\ChartArea;

class ChartAreaTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->ca = new ChartArea;
    }

    public function testConstructorDefaults()
    {
        $this->assertNull($this->ca->left);
        $this->assertNull($this->ca->top);
        $this->assertNull($this->ca->width);
        $this->assertNull($this->ca->height);
    }

    public function testConstructorWithIntsValuesAssignment()
    {
        $chartArea = new ChartArea(array(
            'left'   => 25,
            'top'    => 10,
            'width'  => 900,
            'height' => 300
        ));

        $this->assertEquals(25, $chartArea->left);
        $this->assertEquals(10, $chartArea->top);
        $this->assertEquals(900, $chartArea->width);
        $this->assertEquals(300, $chartArea->height);
    }

    public function testConstructorWithPercentsValuesAssignment()
    {
        $chartArea = new ChartArea(array(
            'left'   => '5%',
            'top'    => '10%',
            'width'  => '90%',
            'height' => '40%'
        ));

        $this->assertEquals('5%', $chartArea->left);
        $this->assertEquals('10%', $chartArea->top);
        $this->assertEquals('90%', $chartArea->width);
        $this->assertEquals('40%', $chartArea->height);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testConstructorWithInvalidPropertiesKey()
    {
        $chartArea = new ChartArea(array('Pizza' => 'sandwich'));
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @dataProvider badParamsProvider
     */
    public function testLeftWithBadParams($badVals)
    {
        $this->ca->left($badVals);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @dataProvider badParamsProvider
     */
    public function testTopWithBadParams($badVals)
    {
        $this->ca->top($badVals);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @dataProvider badParamsProvider
     */
    public function testWidthWithBadParams($badVals)
    {
        $this->ca->width($badVals);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @dataProvider badParamsProvider
     */
    public function testHeightWithBadParams($badVals)
    {
        $this->ca->height($badVals);
    }

    public function badParamsProvider()
    {
        return array(
            array('zooAnimals'),
            array(123.456),
            array(array()),
            array(new \stdClass()),
            array(true),
            array(null)
        );
    }
}
