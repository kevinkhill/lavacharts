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
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testConstructorWithBadTypes($badTypes)
    {
        new Options($badTypes);
    }

    public function testGetOption()
    {
        $this->assertEquals($this->Options->get('food', 'tacos'));
        $this->assertTrue($this->Options->get('chips'));
        $this->assertEquals($this->Options->get('drink')['type'], 'beer');
        $this->assertEquals($this->Options->get('drink')['count'], 3);
    }
}
