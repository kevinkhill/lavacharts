<?php namespace Khill\Lavacharts\Tests\Configs;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Configs\ColorAxis;
use \Mockery as m;

class ColorAxisTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->ca = new ColorAxis;
    }

    public function testConstructorDefaults()
    {
        $this->assertNull($this->ca->minValue);
        $this->assertNull($this->ca->maxValue);
        $this->assertNull($this->ca->values);
        $this->assertNull($this->ca->colors);
    }

    public function testConstructorValuesAssignment()
    {
        $colorAxis = new ColorAxis(array(
            'minValue' => 1,
            'maxValue' => 10,
            'values'   => array('10', 6),
            'colors'   => array('#5B5B5B', 'red')
        ));

        $this->assertEquals(1, $colorAxis->minValue);
        $this->assertEquals(10, $colorAxis->maxValue);
        $this->assertEquals(array(10, 6), $colorAxis->values);
        $this->assertEquals(array('#5B5B5B', 'red'), $colorAxis->colors);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testConstructorWithInvalidPropertiesKey()
    {
        $colorAxis = new ColorAxis(array('Cake' => 'green'));
    }

    public function testMinValueWithNumericParams()
    {
        $this->ca->minValue(5);
        $this->assertEquals(5, $this->ca->minValue);


        $this->ca->minValue('1');
        $this->assertEquals(1, $this->ca->minValue);
    }

    /**
     * @dataProvider nonNumericProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMinValueWithBadTypes($badTypes)
    {
        $this->ca->minValue($badTypes);
    }

    public function testMaxValueWithNumericParams()
    {
        $this->ca->maxValue(50);
        $this->assertEquals(50, $this->ca->maxValue);


        $this->ca->maxValue('18');
        $this->assertEquals(18, $this->ca->maxValue);
    }

    /**
     * @dataProvider nonNumericProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMaxValueWithBadTypes($badTypes)
    {
        $this->ca->maxValue($badTypes);
    }

    public function testValuesWithArrayOfNumbers()
    {
        $this->ca->values(array(1, '18', 54));

        $this->assertEquals(array(1, '18', 54), $this->ca->values);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testValuesWithBadTypes($badTypes)
    {
        $this->ca->values($badTypes);
    }


    public function testColorsWithArrayOfStrings()
    {
        $this->ca->colors(array('red', 'blue'));

        $this->assertEquals(array('red', 'blue'), $this->ca->colors);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testColorsWithBadTypes($badTypes)
    {
        $this->ca->colors($badTypes);
    }
}
