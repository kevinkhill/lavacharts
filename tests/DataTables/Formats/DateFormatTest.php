<?php

namespace Khill\Lavacharts\Tests\Formats;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\DataTables\Formats\DateFormat;

class DateFormatTest extends ProvidersTestCase
{
    /**
     * @covers \Khill\Lavacharts\DataTables\Formats\DateFormat
     */
    public function testConstructorArgs()
    {
        $dateFormat = new DateFormat([
            'formatType' => 'short',
            'pattern'    => 'Y-m-d',
            'timeZone'   => 'PDT'
        ]);

        $this->assertEquals('short', $dateFormat->formatType);
        $this->assertEquals('Y-m-d', $dateFormat->pattern);
        $this->assertEquals('PDT', $dateFormat->timeZone);
    }

    public function testGetType()
    {
        $dateFormat = new DateFormat;

        $this->assertEquals('DateFormat', DateFormat::TYPE);
        $this->assertEquals('DateFormat', $dateFormat->getType());
    }
}
