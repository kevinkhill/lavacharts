<?php

namespace Khill\Lavacharts\Tests\Formats;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\DataTables\Formats\DateFormat;

class ParentFormatTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->options = [
            'formatType' => 'short',
            'pattern'    => 'Y-m-d',
            'timeZone'   => 'PST'
        ];

        $this->dateFormat = new DateFormat($this->options);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testConstructorWithBadTypes($badTypes)
    {
        new DateFormat($badTypes);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testConstructorWithBadOption()
    {
        new DateFormat(['baked'=>'beans']);
    }

    public function testGetOptions()
    {
        $this->assertInstanceOf('\Khill\Lavacharts\Configs\Options', $this->dateFormat->getOptions());
    }

    public function testJsonSerialize()
    {
        $json = '{"formatType":"short","pattern":"Y-m-d","timeZone":"PST"}';

        $this->assertEquals($json, json_encode($this->dateFormat));
    }
}
