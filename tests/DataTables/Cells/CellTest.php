<?php

namespace Khill\Lavacharts\Tests\DataTables\Cells;

use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\DataTables\Cells\Cell;

class CellTest extends ProvidersTestCase
{
    public $Cell;

    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Cells\Cell::__construct
     */
    public function testConstructorArgs()
    {
        $column = new Cell(1, 'low', ['textstyle' => ['fontName' => 'Arial']]);

        $this->assertEquals(1, $this->inspect($column, 'v'));
        $this->assertEquals('low', $this->inspect($column, 'f'));
        //@TODO investigate this
        //$this->assertTrue(is_array($this->inspect($column, 'p')));
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidParamType
     * @covers \Khill\Lavacharts\DataTables\Cells\Cell::__construct
     */
    public function testConstructorArgFormatWithBadType($badTypes)
    {
        new Cell(1, $badTypes);
    }
    
    /**
     * @depends testConstructorArgs
     * @covers \Khill\Lavacharts\DataTables\Cells\Cell::getValue
     */
    public function testGetValue()
    {
        $column = new Cell(1);

        $this->assertEquals(1, $column->getValue());
    }

    /**
     * @depends testConstructorArgs
     * @covers \Khill\Lavacharts\DataTables\Cells\Cell::getFormat
     */
    public function testGetFormat()
    {
        $column = new Cell(1, 'low');

        $this->assertEquals('low', $column->getFormat());
    }

    /**
     * @depends testConstructorArgs
     * @covers \Khill\Lavacharts\DataTables\Cells\Cell::getOptions
     */
    public function testGetOptions()
    {
        $column = new Cell(1, 'low', ['textstyle' => ['fontName' => 'Arial']]);

        $this->assertTrue(is_array($column->getOptions()));
    }

    /**
     * @depends testConstructorArgs
     * @covers \Khill\Lavacharts\DataTables\Cells\Cell::jsonSerialize
     */
    public function testJsonSerialization()
    {
        $column = new Cell(1, 'low', ['textstyle' => ['fontName' => 'Arial']]);

        $json = '{"v":1,"f":"low","p":{"textstyle":{"fontName":"Arial"}}}';

        $this->assertEquals($json, json_encode($column));
    }
}



