<?php

namespace Khill\Lavacharts\Tests\Formats;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\DataTables\Formats\ArrowFormat;

class ArrowFormatTest extends ProvidersTestCase
{
    public $arrowFormat;

    public $json = '{"base":1}';

    public function setUp()
    {
        parent::setUp();

        $this->arrowFormat = new ArrowFormat([
            'base' => 1
        ]);
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Formats\ArrowFormat
     */
    public function testConstructorOptionAssignment()
    {
        $this->assertEquals(1, $this->arrowFormat['base']);
    }

    public function testGetType()
    {
        $this->assertEquals('ArrowFormat', $this->arrowFormat->getType());
    }

    public function testToJsonForOptionsSerialization()
    {
        $this->assertEquals($this->json, $this->arrowFormat->toJson());
    }

    public function testToJavascriptForObjectSerialization()
    {
        $javascript = 'google.visualization.ArrowFormat('.$this->json.')';

        $this->assertEquals($javascript, $this->arrowFormat->toJavascript());
    }
}
