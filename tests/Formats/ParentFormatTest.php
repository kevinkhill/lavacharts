<?php namespace Khill\Lavacharts\Tests\Formats;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Formats\DateFormat;

class ParentFormatTest extends ProvidersTestCase
{

    public function setUp()
    {
        parent::setUp();

        $this->options = array(
            'formatType' => 'short',
            'pattern'    => 'Y-m-d',
            'timeZone'   => 'PST'
        );

        $this->dateFormat = new DateFormat($this->options);
    }

    public function testGetValues()
    {
        $this->assertEquals($this->options, $this->dateFormat->getValues());
    }

    public function testToJson()
    {
        $json = '{"formatType":"short","pattern":"Y-m-d","timeZone":"PST"}';

        $this->assertEquals($json, $this->dateFormat->toJson());
    }
}

