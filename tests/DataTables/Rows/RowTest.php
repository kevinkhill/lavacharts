<?php

namespace Khill\Lavacharts\Tests\DataTables\Rows;

use Khill\Lavacharts\DataTables\Rows\Row;
use Khill\Lavacharts\Tests\ProvidersTestCase;

class RowTest extends ProvidersTestCase
{
    public $Row;

    /**
     * @covers \Khill\Lavacharts\DataTables\Rows\Row::__construct
     */
    public function testConstructorWithNonCarbonValues()
    {
        $row = new Row(['bob', 1, 2.0]);

        $values = $this->inspect($row, 'values');

        $this->assertEquals('bob', $values[0]);
        $this->assertEquals(1, $values[1]);
        $this->assertEquals(2.0, $values[2]);
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Rows\Row::__construct
     */
    public function testConstructorWithCarbon()
    {
        $mockCarbon = \Mockery::mock('\Carbon\Carbon')->makePartial();

        $row = new Row([$mockCarbon, 1, 2.0]);

        $values = $this->inspect($row, 'values');

        $this->assertInstanceOf('\Khill\Lavacharts\DataTables\Cells\DateCell', $values[0]);
        $this->assertEquals(1, $values[1]);
        $this->assertEquals(2.0, $values[2]);
    }

    /**
     * @depends testConstructorWithCarbon
     * @covers \Khill\Lavacharts\DataTables\Rows\Row::getCell
     */
    public function testGetColumnValue()
    {
        $mockCarbon = \Mockery::mock('\Carbon\Carbon')->makePartial();

        $row = new Row([$mockCarbon, 1, 2.0]);

        $this->assertInstanceOf('\Khill\Lavacharts\DataTables\Cells\DateCell', $row->getCell(0));
        $this->assertEquals(1, $row->getCell(1));
        $this->assertEquals(2.0, $row->getCell(2));
    }

    /**
     * @depends testConstructorWithCarbon
     * @dataProvider nonIntProvider
     * @covers \Khill\Lavacharts\DataTables\Rows\Row::getCell
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidColumnIndex
     */
    public function testGetColumnValueWithBadType($badTypes)
    {
        $mockCarbon = \Mockery::mock('\Carbon\Carbon')->makePartial();

        $row = new Row([$mockCarbon, 1, 2.0]);

        $row->getCell($badTypes);
    }

    /**
     * @depends testConstructorWithCarbon
     * @covers \Khill\Lavacharts\DataTables\Rows\Row::getCell
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidColumnIndex
     */
    public function testGetColumnValueWithInvalidColumnIndex()
    {
        $row = new Row([1]);

        $row->getCell(41);
    }

    /**
     * @depends testConstructorWithCarbon
     * @covers \Khill\Lavacharts\DataTables\Rows\Row::jsonSerialize
     */
    public function testJsonSerialization()
    {
        $mockCarbon = \Mockery::mock('\Carbon\Carbon[parse]', ['1988-03-24 1:23:45']);

        $row = new Row([$mockCarbon, 1, 2.1]);

        $json = '{"c":[{"v":"Date(1988,2,24,1,23,45)"},{"v":1},{"v":2.1}]}';

        $this->assertEquals($json, json_encode($row));
    }
}
