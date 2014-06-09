<?php namespace Khill\Lavacharts\Tests\Configs;

use Khill\Lavacharts\Lavacharts;
use Khill\Lavacharts\Tests\TestCase;
use Khill\Lavacharts\Configs\backgroundColor;

class BackgroundColorTest extends TestCase {

    public function setUp()
    {
        parent::setUp();

        $this->bgc = new backgroundColor();
    }

    public function testIfInstanceOfbackgroundColor()
    {
        $this->assertInstanceOf('Khill\Lavacharts\Configs\backgroundColor', $this->bgc);
    }

    public function testConstructorDefaults()
    {
        $this->assertEquals(null, $this->bgc->stroke);
        $this->assertEquals(null, $this->bgc->strokeWidth);
    }

    public function testConstructorValuesAssignment()
    {
        $backgroundColor = new backgroundColor(array(
            'stroke'      => '#E3D5F2',
            'strokeWidth' => 6,
            'fill'        => '#B0C9E3'
        ));

        $this->assertEquals('#E3D5F2', $backgroundColor->stroke);
        $this->assertEquals(6,         $backgroundColor->strokeWidth);
        $this->assertEquals('#B0C9E3', $backgroundColor->fill);
    }

    public function testConstructorWithInvalidPropertiesKey()
    {
        $backgroundColor = new backgroundColor(array('TunaSalad' => 'sandwich'));

        $this->assertTrue(Lavacharts::hasErrors());
    }

    /**
     * @dataProvider badParamsProvider1
     */
    public function testStrokeWithBadParams($badVals)
    {
        $this->bgc->stroke($badVals);

        $this->assertTrue(Lavacharts::hasErrors());
    }

    /**
     * @dataProvider badParamsProvider2
     */
    public function testStrokeWidthWithBadParams($badVals)
    {
        $this->bgc->strokeWidth($badVals);

        $this->assertTrue(Lavacharts::hasErrors());
    }

    /**
     * @dataProvider badParamsProvider1
     */
    public function testFillWithBadParams($badVals)
    {
        $this->bgc->fill($badVals);

        $this->assertTrue(Lavacharts::hasErrors());
    }

    public function badParamsProvider1()
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

    public function badParamsProvider2()
    {
        return array(
            array('fruitsAndVeggies'),
            array(123.456),
            array(array()),
            array(new \stdClass()),
            array(true),
            array(null)
        );
    }

}

