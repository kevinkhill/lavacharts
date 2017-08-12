<?php

namespace Khill\Lavacharts\Tests\DataTables;

use Carbon\Carbon;
use Khill\Lavacharts\DataTables\Cells\Cell;
use Khill\Lavacharts\DataTables\Cells\DateCell;
use Khill\Lavacharts\DataTables\Row;
use Khill\Lavacharts\Tests\ProvidersTestCase;
use Mockery;

/**
 * @property string nullRowJson
 */
class RowTest extends ProvidersTestCase
{
    /**
     * @covers \Khill\Lavacharts\DataTables\Row::__construct()
     * @covers \Khill\Lavacharts\DataTables\Row::getCell()
     * @covers \Khill\Lavacharts\DataTables\Row::getCellValue()
     */
    public function testCreatingRowWithScalarValues()
    {
        $row = new Row(['bob', 1, 2.0]);

        $this->assertInstanceOf(Cell::class, $row->getCell(0));
        $this->assertEquals('bob', $row->getCellValue(0));

        $this->assertInstanceOf(Cell::class, $row->getCell(1));
        $this->assertEquals(1, $row->getCellValue(1));

        $this->assertInstanceOf(Cell::class, $row->getCell(2));
        $this->assertEquals(2.0, $row->getCellValue(2));
    }

    /**
     * @depends testCreatingRowWithScalarValues
     */
    public function testCreatingRowWithCarbonAndScalarValues()
    {
        $mockCarbon = Mockery::mock(Carbon::class);

        $row = new Row([$mockCarbon, 1, 2.0]);

        $this->assertInstanceOf(DateCell::class, $cell = $row->getCell(0));
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Row::toArray()
     */
    public function testRowToArray()
    {
        $row = new Row([2, 1, 2.1]);

        $rowArr = $row->toArray();

        $this->assertTrue(is_array($rowArr));

        $this->assertArrayHasKey('c', $rowArr);
        $this->assertTrue(is_array($rowArr['c']));
        $this->assertCount(3, $rowArr['c']);

        $this->assertTrue(is_array($rowArr['c'][0]));
        $this->assertArrayHasKey('v', $rowArr['c'][0]);
        $this->assertEquals(2, $rowArr['c'][0]['v']);

        $this->assertTrue(is_array($rowArr['c'][1]));
        $this->assertArrayHasKey('v', $rowArr['c'][1]);
        $this->assertEquals(1, $rowArr['c'][1]['v']);

        $this->assertTrue(is_array($rowArr['c'][2]));
        $this->assertArrayHasKey('v', $rowArr['c'][2]);
        $this->assertEquals(2.1, $rowArr['c'][2]['v']);
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Row::jsonSerialize()
     */
    public function testRowJsonSerializationWithScalarValues()
    {
        $row = new Row([2, 1, 2.1]);

        $this->assertEquals(
            '{"c":[{"v":2},{"v":1},{"v":2.1}]}',
            json_encode($row)
        );
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Row::jsonSerialize()
     */
    public function testRowJsonSerializationWithCarbon()
    {$this->markTestIncomplete('[RowTest::testRowJsonSerializationWithCarbon] Need to figure out what is going on here.....');
        $mockCarbon = Mockery::mock(Carbon::class.'[parse]', ['1988-03-24 1:23:45']);

        $row = new Row([$mockCarbon, 1, 2.1]);

        $this->assertEquals(
            '{"c":[{"v":"Date(1988,2,24,1,23,45)"},{"v":1},{"v":2.1}]}',
            json_encode($row)
        );
    }

    /**
     * @depends testRowJsonSerializationWithScalarValues
     * @covers \Khill\Lavacharts\DataTables\Row::toJson()
     */
    public function testRowToJsonWithScalarValues()
    {
        $row = new Row([2, 1, 2.1]);

        $this->assertEquals(
            '{"c":[{"v":2},{"v":1},{"v":2.1}]}',
            $row->toJson()
        );
    }

    /**
     * @depends testRowJsonSerializationWithScalarValues
     * @covers \Khill\Lavacharts\DataTables\Row::toJson()
     */
    public function testRowToJsonWithNulls()
    {
        $row = new Row([null, null, null]);

        $this->assertEquals(
            '{"c":[{"v":null},{"v":null},{"v":null}]}',
            $row->toJson()
        );
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidColumnIndex
     */
    public function testGetCellWithInvalidColumnIndexType()
    {
        $row = new Row([1, 1, 2.0]);

        $row->getCell('purple');
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Row::getCell
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidColumnIndex
     */
    public function testGetCellWithColumnIndexOutOfRange()
    {
        $row = new Row([1]);

        $row->getCell(2);
    }
}
