<?php

namespace Khill\Lavacharts\Tests\Formats;

use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\DataTables\Formats\DateFormat;

/**
 * @property \Khill\Lavacharts\DataTables\Formats\DateFormat dateFormat
 */
class DateFormatTest extends ProvidersTestCase
{
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
     * @covers \Khill\Lavacharts\DataTables\Formats\DateFormat
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

    public function testGetJsClass()
    {
        $jsClass = 'google.visualization.DateFormat';

        $this->assertEquals($jsClass, $this->dateFormat->getJsClass());
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Formats\DateFormat::toJson()
     */
    public function testToJson()
    {
        $this->assertEquals($this->json, $this->dateFormat->toJson());
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Formats\DateFormat::jsonSerialize()
     */
    public function testJsonSerialization()
    {
        $this->assertEquals($this->json, json_encode($this->dateFormat));
    }
}
