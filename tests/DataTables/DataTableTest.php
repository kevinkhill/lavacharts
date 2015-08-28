<?php

namespace Khill\Lavacharts\Tests\DataTables;

use \Khill\Lavacharts\DataTables\DataTable;
use \Khill\Lavacharts\Tests\ProvidersTestCase;
use Carbon\Carbon;
use \Mockery as m;

class DataTableTest extends ProvidersTestCase
{
    /**
     * @var \Khill\Lavacharts\DataTables\DataTable
     */
    public $dt;

    public function setUp()
    {
        parent::setUp();

        $this->dt = new DataTable();
    }

    public function testDefaultTimezoneUponCreation()
    {
        $this->assertEquals($this->dt->timezone, 'America/Los_Angeles');
    }

    public function testSetTimezoneWithConstructor()
    {
        $dt = new DataTable('America/New_York');

        $this->assertEquals($dt->timezone, 'America/New_York');
    }

    public function testSetTimezoneMethod()
    {
        $this->dt->setTimezone('America/New_York');

        $this->assertEquals($this->dt->timezone, 'America/New_York');
    }

    public function testSetDateTimeFormat()
    {
        $this->dt->setDateTimeFormat('YYYY-mm-dd');

        $format = $this->getPrivateProperty($this->dt, 'dateTimeFormat');

        $this->assertEquals($format, 'YYYY-mm-dd');
    }

    /**
     * @depends testSetDateTimeFormat
     */
    public function testGetDateTimeFormat()
    {
        $this->dt->setDateTimeFormat('YYYY-mm-dd');

        $this->assertEquals($this->dt->getDateTimeFormat(), 'YYYY-mm-dd');
    }

    /**
     * @dataProvider nonStringProvider
     */
    public function testIniDefaultTimezoneWithBadValues($badValues)
    {
        date_default_timezone_set('America/Los_Angeles');

        $dt = new DataTable($badValues);

        $this->assertEquals($dt->timezone, 'America/Los_Angeles');
    }

    public function testAddColumnWithType()
    {
        $this->dt->addColumn('date');

        $column = $this->getPrivateProperty($this->dt, 'cols')[0];

        $this->assertEquals($column->getType(), 'date');
    }

    public function testAddDateColumn()
    {
        $this->dt->addDateColumn();

        $column = $this->getPrivateProperty($this->dt, 'cols')[0];

        $this->assertEquals($column->getType(), 'date');
    }

    public function testAddNumberColumn()
    {
        $this->dt->addNumberColumn();

        $column = $this->getPrivateProperty($this->dt, 'cols')[0];

        $this->assertEquals($column->getType(), 'number');
    }

    public function testAddStringColumn()
    {
        $this->dt->addStringColumn();

        $column = $this->getPrivateProperty($this->dt, 'cols')[0];

        $this->assertEquals($column->getType(), 'string');
    }

    public function testAddColumnWithArrayOfType()
    {
        $this->dt->addColumn(['date']);

        $column = $this->getPrivateProperty($this->dt, 'cols')[0];

        $this->assertEquals($column->getType(), 'date');
    }

    public function testAddColumnWithTypeAndDescription()
    {
        $this->dt->addColumn('date', 'Days in March');

        $column = $this->getPrivateProperty($this->dt, 'cols')[0];

        $this->assertEquals($column->getType(), 'date');
        $this->assertEquals($column->getLabel(), 'Days in March');
    }

    public function testAddColumnWithArrayOfTypeAndDescription()
    {
        $this->dt->addColumn(['date', 'Days in March']);

        $column = $this->getPrivateProperty($this->dt, 'cols')[0];

        $this->assertEquals($column->getType(), 'date');
        $this->assertEquals($column->getLabel(), 'Days in March');
    }

    public function testAddColumnsWithArrayOfTypeAndDescription()
    {
        $this->dt->addColumns([
            ['date', 'Days in March'],
            ['number', 'Day of the Week'],
            ['number', 'Temperature'],
        ]);

        $columns = $this->getPrivateProperty($this->dt, 'cols');

        $this->assertEquals($columns[0]->getType(), 'date');
        $this->assertEquals($columns[0]->getLabel(), 'Days in March');

        $this->assertEquals($columns[1]->getType(), 'number');
        $this->assertEquals($columns[1]->getLabel(), 'Day of the Week');

        $this->assertEquals($columns[2]->getType(), 'number');
        $this->assertEquals($columns[2]->getLabel(), 'Temperature');
    }

    /**
     * @depends testAddColumnWithType
     */
    public function testAddRowWithDate()
    {
        $this->dt->addColumn('date');
        $this->dt->addRow([Carbon::parse('March 24th, 1988')]);

        $column = $this->getPrivateProperty($this->dt, 'cols')[0];
        $row    = $this->getPrivateProperty($this->dt, 'rows')[0];

        $this->assertEquals($column->getType(), 'date');
        $this->assertEquals($row->getColumnValue(0), 'Date(1988,2,24,0,0,0)');
    }

    /**
     * @depends testAddColumnWithTypeAndDescription
     */
    public function testAddRowWithMultipleColumnsWithDateAndNumbers()
    {
        $this->dt->addColumn('date');
        $this->dt->addColumn('number');
        $this->dt->addColumn('number');

        $this->dt->addRow([Carbon::parse('March 24th, 1988'), 12345, 67890]);

        $columns = $this->getPrivateProperty($this->dt, 'cols');
        $row     = $this->getPrivateProperty($this->dt, 'rows')[0];

        $this->assertEquals($columns[0]->getType(), 'date');
        $this->assertEquals($columns[1]->getType(), 'number');
        $this->assertEquals($columns[2]->getType(), 'number');

        $this->assertEquals($row->getColumnValue(0), 'Date(1988,2,24,0,0,0)');
        $this->assertEquals($row->getColumnValue(1), 12345);
        $this->assertEquals($row->getColumnValue(2), 67890);
    }

    /**
     * @depends testAddColumnWithTypeAndDescription
     */
    public function testAddRowsWithMultipleColumnsWithDateAndNumbers()
    {
        $this->dt->addColumn('date');
        $this->dt->addColumn('number');
        $this->dt->addColumn('number');

        $rows = [
            [Carbon::parse('March 24th, 1988'), 12345, 67890],
            [Carbon::parse('March 25th, 1988'), 1122, 3344]
        ];

        $this->dt->addRows($rows);

        $columns = $this->getPrivateProperty($this->dt, 'cols');
        $rows    = $this->getPrivateProperty($this->dt, 'rows');

        $this->assertEquals($columns[0]->getType(), 'date');
        $this->assertEquals($columns[1]->getType(), 'number');
        $this->assertEquals($columns[2]->getType(), 'number');

        $this->assertEquals($rows[0]->getColumnValue(0), 'Date(1988,2,24,0,0,0)');
        $this->assertEquals($rows[0]->getColumnValue(1), 12345);
        $this->assertEquals($rows[0]->getColumnValue(2), 67890);

        $this->assertEquals($rows[1]->getColumnValue(0), 'Date(1988,2,25,0,0,0)');
        $this->assertEquals($rows[1]->getColumnValue(1), 1122);
        $this->assertEquals($rows[1]->getColumnValue(2), 3344);
    }

    /**
     * @depends testAddColumnWithType
     * @depends testAddRowsWithMultipleColumnsWithDateAndNumbers
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidRowDefinition
     */
    public function testAddRowsWithBadColumnValue()
    {
        $this->dt->addColumn('date');
        $this->dt->addColumn('number');

        $rows = [
            [Carbon::parse('March 24th, 1988'), 12345],
            234.234
        ];

        $this->dt->addRows($rows);
    }

    /**
     * @depends testAddColumnWithType
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidCellCount
     */
    public function testAddRowWithMoreCellsThanColumns()
    {
        $this->dt->addColumn('date');
        $this->dt->addColumn('number');

        $this->dt->addRow([Carbon::parse('March 24th, 1988'), 12345, 67890]);
    }

    /**
     * @depends testAddColumnWithTypeAndDescription
     * @dataProvider nonCarbonOrDateStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidDateTimeString
     */
    public function testAddRowWithBadDateTypes($badDate)
    {
        $this->dt->addColumn('date');

        $this->dt->addRow([$badDate]);
    }

    /**
     * @depends testAddColumnWithTypeAndDescription
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidRowProperty
     */
    public function testAddRowWithEmptyArray()
    {
        $this->dt->addColumn('date');

        $this->dt->addRow([[]]);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidColumnType
     */
    public function testAddColumnsWithBadValuesInArray()
    {
        $this->dt->addColumns([
            [5, 'falcons'],
            [false, 'tacos']
        ]);
    }

    /**
     * @depends testAddColumnWithType
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidColumnIndex
     */
    public function testFormatColumnWithBadColumnLabel()
    {
        $mockDateFormat = m::mock('Khill\Lavacharts\DataTables\Formats\DateFormat');

        $this->dt->addColumn('date');

        $this->dt->formatColumn('grizzly', $mockDateFormat);
    }

     /**
     * @depends testAddColumnWithTypeAndDescription
     */
    public function testAddRowsWithMultipleColumnsWithDateTimeAndNumbers()
    {
        $this->dt->addColumns([
            ['datetime'],
            ['number'],
            ['number']
        ])->addRows([
            [Carbon::parse('March 24th, 1988 8:01:05'), 12345, 67890],
            [Carbon::parse('March 25th, 1988 8:02:06'), 1122, 3344]
        ]);

        $columns = $this->getPrivateProperty($this->dt, 'cols');
        $rows    = $this->getPrivateProperty($this->dt, 'rows');

        $this->assertEquals($columns[0]->getType(), 'datetime');
        $this->assertEquals($columns[1]->getType(), 'number');
        $this->assertEquals($columns[2]->getType(), 'number');

        $this->assertEquals($rows[0]->getColumnValue(0), 'Date(1988,2,24,8,1,5)');
        $this->assertEquals($rows[0]->getColumnValue(1), 12345);
        $this->assertEquals($rows[0]->getColumnValue(2), 67890);

        $this->assertEquals($rows[1]->getColumnValue(0), 'Date(1988,2,25,8,2,6)');
        $this->assertEquals($rows[1]->getColumnValue(1), 1122);
        $this->assertEquals($rows[1]->getColumnValue(2), 3344);
    }

    /**
     * @depends testAddColumnWithTypeAndDescription
     */
    public function testGetColumns()
    {
        $this->dt->addColumn('date', 'Test1');
        $this->dt->addColumn('number', 'Test2');

        $cols = $this->dt->getColumns();

        $this->assertEquals($cols[0]->getType(), 'date');
        $this->assertEquals($cols[1]->getType(), 'number');
    }

    /**
     * @depends testAddColumnWithType
     * @depends testAddRowWithDate
     * @covers \Khill\Lavacharts\DataTables\DateCell::__toString
     * @covers \Khill\Lavacharts\DataTables\DateCell::__toString
     */
    public function testGetRows()
    {
        $this->dt->addColumn('date');

        $this->dt->addRow([Carbon::parse('March 24th, 1988')]);
        $this->dt->addRow([Carbon::parse('March 25th, 1988')]);

        $rows = $this->dt->getRows();

        $this->assertEquals($rows[0]->getColumnValue(0), 'Date(1988,2,24,0,0,0)');
        $this->assertEquals($rows[1]->getColumnValue(0), 'Date(1988,2,25,0,0,0)');
    }


    /**********************************************************************************
     * DateCell Tests
     **********************************************************************************/

    /**
     * @depends testAddDateColumn
     * @covers \Khill\Lavacharts\DataTables\DateCell::parseString
     */
    public function testDateCellParseString()
    {
        $this->dt->addDateColumn();

        $this->dt->addRow(['March 24th, 1988 8:01:05']);

        $row = $this->getPrivateProperty($this->dt, 'rows')[0];

        $this->assertEquals($row->getColumnValue(0), 'Date(1988,2,24,8,1,5)');
    }

    /**
     * @depends testAddDateColumn
     * @depends testSetDateTimeFormat
     * @covers \Khill\Lavacharts\DataTables\DateCell::parseString
     */
    public function testDateCellParseStringWithFormat()
    {
        $this->dt->setDateTimeFormat('YYYY-mm')->addDateColumn();

        $this->dt->addRows([
            ['1988-03'],
            ['1988-04']
        ]);

        $rows = $this->getPrivateProperty($this->dt, 'rows');

        $this->assertEquals($rows->getColumnValue(0), 'Date(1988,2,0,0,0,0)');
        $this->assertEquals($rows->getColumnValue(0), 'Date(1988,3,0,0,0,0)');
    }

    /**
     * @depends testAddDateColumn
     * @depends testAddRowWithDate
     * @covers \Khill\Lavacharts\DataTables\DateCell::jsonSerialize
     */
    public function testJsonSerializationOfDateCells()
    {
        $this->dt->addDateColumn();

        $this->dt->addRow([Carbon::parse('March 24th, 1988 8:01:05')]);

        $row = $this->getPrivateProperty($this->dt, 'rows')[0];

        $this->assertEquals(json_encode($row->getColumnValue(0)), '"Date(1988,2,24,8,1,5)"');
    }
}

