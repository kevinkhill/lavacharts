<?php

namespace Khill\Lavacharts\Tests\Formats;

use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\DataTables\Formats\ArrowFormat;

/**
 * @property \Khill\Lavacharts\DataTables\Formats\ArrowFormat arrowFormat
 */
class ArrowFormatTest extends ProvidersTestCase
{
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

    public function testGetJsClass()
    {
        $jsClass = 'google.visualization.ArrowFormat';

        $this->assertEquals($jsClass, $this->arrowFormat->getJsClass());
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Formats\ArrowFormat::toJson()
     */
    public function testToJson()
    {
        $this->assertEquals($this->json, $this->arrowFormat->toJson());
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Formats\ArrowFormat::jsonSerialize()
     */
    public function testJsonSerialization()
    {
        $this->assertEquals($this->json, json_encode($this->arrowFormat));
    }
}
