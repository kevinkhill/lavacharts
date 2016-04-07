<?php

namespace Khill\Lavacharts\Tests\Formats;

use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\DataTables\Formats\ArrowFormat;

class ArrowFormatTest extends ProvidersTestCase
{
    /**
     * @covers \Khill\Lavacharts\DataTables\Formats\ArrowFormat
     */
    public function testConstructorArgs()
    {
        $arrowFormat = new ArrowFormat([
            'base' => 1
        ]);

        $this->assertEquals(1, $arrowFormat->base);
    }

    public function testGetType()
    {
        $arrowFormat = new ArrowFormat;

        $this->assertEquals('ArrowFormat', ArrowFormat::TYPE);
        $this->assertEquals('ArrowFormat', $arrowFormat->getType());
    }
}
