<?php

namespace Khill\Lavacharts\Tests\Configs;

use \Khill\Lavacharts\Configs\Options;
use \Khill\Lavacharts\Tests\ProvidersTestCase;

class OptionsTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->Options = new Options([
            'food' => 'tacos',
            'chips' => true,
            'drink' => [
                'type' => 'beer',
                'count' => 3
            ]
        ]);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidOptions
     */
    public function testConstructorWithBadTypes($badTypes)
    {
        new Options($badTypes);
    }

    public function testGetOption()
    {
        $this->assertEquals($this->Options['food'], 'tacos');
        $this->assertTrue($this->Options['chips']);
        $this->assertEquals($this->Options['drink']['type'], 'beer');
        $this->assertEquals($this->Options['drink']['count'], 3);
    }
}
