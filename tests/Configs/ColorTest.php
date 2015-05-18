<?php namespace Khill\Lavacharts\Tests\Configs;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Configs\Color;
use \Mockery as m;

class ColorTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->c = new Color;
    }

    public function testConstructorDefaults()
    {
        $this->assertNull($this->c->color);
        $this->assertNull($this->c->backgroundColor);
        $this->assertNull($this->c->opacity);
    }

    public function testConstructorValuesAssignment()
    {
        $colorAxis = new Color([
            'color'           => '#5B5B5B',
            'backgroundColor' => 'red',
            'opacity'         => 0.8
        ]);

        $this->assertEquals('#5B5B5B', $colorAxis->color);
        $this->assertEquals('red', $colorAxis->backgroundColor);
        $this->assertEquals(0.8, $colorAxis->opacity);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testConstructorWithInvalidPropertiesKey()
    {
        new Color(['Soda' => 'Coke']);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testColorWithBadParams($badParams)
    {
        $this->c->color($badParams);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testBackgroundColorWithBadParams($badParams)
    {
        $this->c->backgroundColor($badParams);
    }

    /**
     * @dataProvider nonFloatProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testOpacityWithBadParams($badParams)
    {
        $this->c->opacity($badParams);
    }
}
