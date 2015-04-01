<?php namespace Khill\Lavacharts\Tests\Configs;

use \Khill\Lavacharts\Configs\Gradient;

class GradientTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->g = new Gradient;
    }

    public function testConstructorDefaults()
    {
        $this->assertNull($this->g->color1);
        $this->assertNull($this->g->color2);
        $this->assertNull($this->g->x1);
        $this->assertNull($this->g->y1);
        $this->assertNull($this->g->x2);
        $this->assertNull($this->g->y2);
    }

    public function testConstructorValuesAssignment()
    {
        $gradient = new Gradient(array(
            'color1' => '#F0F0F0',
            'color2' => '#3D3D3D',
            'x1'     => '0%',
            'y1'     => '0%',
            'x2'     => '100%',
            'y2'     => '100%'
        ));

        $this->assertEquals('#F0F0F0', $gradient->color1);
        $this->assertEquals('#3D3D3D', $gradient->color2);
        $this->assertEquals('0%', $gradient->x1);
        $this->assertEquals('0%', $gradient->y1);
        $this->assertEquals('100%', $gradient->x2);
        $this->assertEquals('100%', $gradient->y2);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testConstructorWithInvalidPropertiesKey()
    {
        new Gradient(array('tacos' => '#F8C3B0'));
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @dataProvider badParamsProvider
     */
    public function testColor1WithBadParams($badVals)
    {
        $this->g->color1($badVals);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @dataProvider badParamsProvider
     */
    public function testColor2WithBadParams($badVals)
    {
        $this->g->color2($badVals);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @dataProvider badParamsProvider
     */
    public function testX1ColorWithBadParams($badVals)
    {
        $this->g->x1($badVals);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @dataProvider badParamsProvider
     */
    public function testY1ColorWithBadParams($badVals)
    {
        $this->g->y1($badVals);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @dataProvider badParamsProvider
     */
    public function testX2ColorWithBadParams($badVals)
    {
        $this->g->x2($badVals);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @dataProvider badParamsProvider
     */
    public function testY2ColorWithBadParams($badVals)
    {
        $this->g->y2($badVals);
    }


    public function badParamsProvider()
    {
        return array(
            array(123),
            array(123.456),
            array(array()),
            array(new \stdClass()),
            array(true),
            array(null)
        );
    }
}
