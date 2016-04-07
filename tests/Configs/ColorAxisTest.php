<?php

namespace Khill\Lavacharts\Tests\Configs;

use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\Configs\ColorAxis;

class ColorAxisTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->ca = new ColorAxis;
    }

    public function testConstructorValuesAssignment()
    {
        $colorAxis = new ColorAxis([
            'minValue' => 1,
            'maxValue' => 10,
            'values'   => ['10', 6],
            'colors'   => ['#5B5B5B', 'red']
        ]);

        $this->assertEquals(1, $colorAxis->minValue);
        $this->assertEquals(10, $colorAxis->maxValue);
        $this->assertEquals([10, 6], $colorAxis->values);
        $this->assertEquals(['#5B5B5B', 'red'], $colorAxis->colors);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testConstructorWithInvalidPropertiesKey()
    {
        new ColorAxis(['Cake' => 'green']);
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
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
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
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMaxValueWithBadTypes($badTypes)
    {
        $this->ca->maxValue($badTypes);
    }

    public function testValuesWithArrayOfNumbers()
    {
        $this->ca->values([1, '18', 54]);

        $this->assertEquals([1, '18', 54], $this->ca->values);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testValuesWithBadTypes($badTypes)
    {
        $this->ca->values($badTypes);
    }


    public function testColorsWithArrayOfStrings()
    {
        $this->ca->colors(['red', 'blue']);

        $this->assertEquals(['red', 'blue'], $this->ca->colors);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testColorsWithBadTypes($badTypes)
    {
        $this->ca->colors($badTypes);
    }
}
