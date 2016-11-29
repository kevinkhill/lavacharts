<?php

namespace Khill\Lavacharts\Tests\Formats;

use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\DataTables\Formats\DateFormat;

class DateFormatTest extends ProvidersTestCase
{
    public $dateFormat;

    public $json = '{"formatType":"short","pattern":"Y-m-d","timeZone":"PDT"}';

    public function setUp()
    {
        $this->dateFormat = new DateFormat([
            'formatType' => 'short',
            'pattern'    => 'Y-m-d',
            'timeZone'   => 'PDT'
        ]);
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Formats\BarFormat
     */
    public function testConstructorOptionAssignment()
    {
        $this->assertEquals('short', $this->dateFormat['formatType']);
        $this->assertEquals('Y-m-d', $this->dateFormat['pattern']);
        $this->assertEquals('PDT',   $this->dateFormat['timeZone']);
    }

    public function testGetType()
    {
        $this->dateFormat = new DateFormat;

        $this->assertEquals('DateFormat', $this->dateFormat->getType());
    }

    public function testToJsonForOptionsSerialization()
    {
        $this->assertEquals($this->json, $this->dateFormat->toJson());
    }

    /**
     * @depends testToJsonForOptionsSerialization
     */
    public function testToJavascriptForObjectSerialization()
    {
        $javascript = 'google.visualization.DateFormat('.$this->json.')';

        $this->assertEquals($javascript, $this->dateFormat->toJavascript());
    }
}
