<?php

namespace Khill\Lavacharts\Tests\DataTables;

use \Khill\Lavacharts\DataTables\DataTable;
use \Khill\Lavacharts\Tests\ProvidersTestCase;
use Carbon\Carbon;
use \Mockery as m;

class DataTableTest extends ProvidersTestCase
{
    public $DataTable;

    public function setUp()
    {
        parent::setUp();

        date_default_timezone_set('America/Los_Angeles');

        $this->DataTable = new DataTable();
    }

    public function testDefaultTimezoneUponCreation()
    {
        $tz = $this->getPrivateProperty($this->DataTable, 'timezone');

        $this->assertEquals('America/Los_Angeles', $tz->getName());
    }

    public function testSetTimezoneWithConstructor()
    {
        $datatable = new DataTable('America/New_York');

        $tz = $this->getPrivateProperty($datatable, 'timezone');

        $this->assertEquals('America/New_York', $tz->getName());
    }

    public function testSetTimezoneMethod()
    {
        $this->DataTable->setTimezone('America/New_York');

        $tz = $this->getPrivateProperty($this->DataTable, 'timezone');

        $this->assertEquals('America/New_York', $tz->getName());
    }

    /**
     * @depends testSetTimezoneMethod
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidTimeZone
     */
    public function testSetTimezoneWithBadType($badVals)
    {
        $this->DataTable->setTimezone($badVals);
    }

    /**
     * @depends testSetTimezoneMethod
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidTimeZone
     */
    public function testSetTimezoneWithInvalidTimezone($badVals)
    {
        $this->DataTable->setTimezone('Murica');
    }

    /**
     * @depends testSetTimezoneMethod
     */
    public function testGetTimezoneMethod()
    {
        $this->DataTable->setTimezone('America/New_York');

        $this->assertInstanceOf('DateTimeZone', $this->DataTable->getTimezone());
        $this->assertEquals('America/New_York', $this->DataTable->getTimezone()->getName());
    }

    public function testSetDateTimeFormat()
    {
        $this->DataTable->setDateTimeFormat('YYYY-mm-dd');

        $format = $this->getPrivateProperty($this->DataTable, 'dateTimeFormat');

        $this->assertEquals('YYYY-mm-dd', $format);
    }

    /**
     * @depends testSetDateTimeFormat
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testSetDateTimeFormatWithBadTypes($badVals)
    {
        $this->DataTable->setDateTimeFormat($badVals);
    }

    /**
     * @depends testSetDateTimeFormat
     */
    public function testGetDateTimeFormat()
    {
        $this->DataTable->setDateTimeFormat('YYYY-mm-dd');

        $this->assertEquals('YYYY-mm-dd', $this->DataTable->getDateTimeFormat());
    }

    public function testGetClone()
    {
        $datatable = $this->DataTable->getClone();

        $this->assertInstanceOf('\Khill\Lavacharts\DataTables\DataTable', $datatable);
        $this->assertEquals($this->DataTable, $datatable);
    }

    public function testAddColumnWithType()
    {
        $this->DataTable->addColumn('date');

        $column = $this->getPrivateProperty($this->DataTable, 'cols')[0];

        $this->assertEquals('date', $column->getType());
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAddColumnWithBadTypes()
    {
        $this->DataTable->addColumn(1);
        $this->DataTable->addColumn(1.1);
        $this->DataTable->addColumn(false);
        $this->DataTable->addColumn(new \stdClass());
    }

    public function testAddDateColumn()
    {
        $this->DataTable->addDateColumn();

        $column = $this->getPrivateProperty($this->DataTable, 'cols')[0];

        $this->assertEquals('date', $column->getType());
    }

    public function testAddNumberColumn()
    {
        $this->DataTable->addNumberColumn();

        $column = $this->getPrivateProperty($this->DataTable, 'cols')[0];

        $this->assertEquals('number', $column->getType());
    }

    public function testAddStringColumn()
    {
        $this->DataTable->addStringColumn();

        $column = $this->getPrivateProperty($this->DataTable, 'cols')[0];

        $this->assertEquals('string', $column->getType());
    }

    public function testAddColumnWithArrayOfType()
    {
        $this->DataTable->addColumn(['date']);

        $column = $this->getPrivateProperty($this->DataTable, 'cols')[0];

        $this->assertEquals('date', $column->getType());
    }

    public function testAddColumnWithTypeAndDescription()
    {
        $this->DataTable->addColumn('date', 'Days in March');

        $column = $this->getPrivateProperty($this->DataTable, 'cols')[0];

        $this->assertEquals('date', $column->getType());
        $this->assertEquals('Days in March', $column->getLabel());
    }

    public function testAddColumnWithArrayOfTypeAndDescription()
    {
        $this->DataTable->addColumn(['date', 'Days in March']);

        $column = $this->getPrivateProperty($this->DataTable, 'cols')[0];

        $this->assertEquals('date', $column->getType());
        $this->assertEquals('Days in March', $column->getLabel());
    }

    public function testAddColumnsWithArrayOfTypeAndDescription()
    {
        $this->DataTable->addColumns([
            ['date', 'Days in March'],
            ['number', 'Day of the Week'],
            ['number', 'Temperature'],
        ]);

        $columns = $this->getPrivateProperty($this->DataTable, 'cols');

        $this->assertEquals('date', $columns[0]->getType());
        $this->assertEquals('Days in March', $columns[0]->getLabel());

        $this->assertEquals('number', $columns[1]->getType());
        $this->assertEquals('Day of the Week', $columns[1]->getLabel());

        $this->assertEquals('number', $columns[2]->getType());
        $this->assertEquals('Temperature', $columns[2]->getLabel());
    }

    /**
     * @depends testAddDateColumn
     */
    public function testAddRowWithEmptyArrayForNull()
    {
        $this->DataTable->addDateColumn();
        $this->DataTable->addRow([]);

        $row = $this->getPrivateProperty($this->DataTable, 'rows')[0];

        $this->assertEquals(null, $row->getColumnValue(0));
    }

    /**
     * @depends testAddDateColumn
     */
    public function testAddRowWithNull()
    {
        $this->DataTable->addDateColumn();
        $this->DataTable->addRow(null);

        $row = $this->getPrivateProperty($this->DataTable, 'rows')[0];

        $this->assertEquals(null, $row->getColumnValue(0));
    }

    /**
     * @depends testAddColumnWithType
     */
    public function testAddRowWithDate()
    {
        $this->DataTable->addColumn('date');
        $this->DataTable->addRow([Carbon::parse('March 24th, 1988')]);

        $column = $this->getPrivateProperty($this->DataTable, 'cols')[0];
        $row    = $this->getPrivateProperty($this->DataTable, 'rows')[0];

        $this->assertEquals('date', $column->getType());
        $this->assertEquals('Date(1988,2,24,0,0,0)', $row->getColumnValue(0));
    }

    /**
     * @depends testAddColumnWithTypeAndDescription
     */
    public function testAddRowWithMultipleColumnsWithDateAndNumbers()
    {
        $this->DataTable->addColumn('date');
        $this->DataTable->addColumn('number');
        $this->DataTable->addColumn('number');

        $this->DataTable->addRow([Carbon::parse('March 24th, 1988'), 12345, 67890]);

        $columns = $this->getPrivateProperty($this->DataTable, 'cols');
        $row     = $this->getPrivateProperty($this->DataTable, 'rows')[0];

        $this->assertEquals('date', $columns[0]->getType());
        $this->assertEquals('number', $columns[1]->getType());
        $this->assertEquals('number', $columns[2]->getType());

        $this->assertEquals('Date(1988,2,24,0,0,0)', $row->getColumnValue(0));
        $this->assertEquals(12345, $row->getColumnValue(1));
        $this->assertEquals(67890, $row->getColumnValue(2));
    }

    /**
     * @depends testAddColumnWithTypeAndDescription
     */
    public function testAddRowsWithMultipleColumnsWithDateAndNumbers()
    {
        $this->DataTable->addColumn('date');
        $this->DataTable->addColumn('number');
        $this->DataTable->addColumn('number');

        $rows = [
            [Carbon::parse('March 24th, 1988'), 12345, 67890],
            [Carbon::parse('March 25th, 1988'), 1122, 3344]
        ];

        $this->DataTable->addRows($rows);

        $columns = $this->getPrivateProperty($this->DataTable, 'cols');
        $rows    = $this->getPrivateProperty($this->DataTable, 'rows');

        $this->assertEquals('date', $columns[0]->getType());
        $this->assertEquals('number', $columns[1]->getType());
        $this->assertEquals('number', $columns[2]->getType());

        $this->assertEquals('Date(1988,2,24,0,0,0)', $rows[0]->getColumnValue(0));
        $this->assertEquals(12345, $rows[0]->getColumnValue(1));
        $this->assertEquals(67890, $rows[0]->getColumnValue(2));

        $this->assertEquals('Date(1988,2,25,0,0,0)', $rows[1]->getColumnValue(0));
        $this->assertEquals(1122, $rows[1]->getColumnValue(1));
        $this->assertEquals(3344, $rows[1]->getColumnValue(2));
    }

    /**
     * @depends testAddColumnWithType
     * @depends testAddRowsWithMultipleColumnsWithDateAndNumbers
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidRowDefinition
     */
    public function testAddRowsWithBadColumnValue()
    {
        $this->DataTable->addColumn('date');
        $this->DataTable->addColumn('number');

        $rows = [
            [Carbon::parse('March 24th, 1988'), 12345],
            234.234
        ];

        $this->DataTable->addRows($rows);
    }

    /**
     * @depends testAddColumnWithType
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidCellCount
     */
    public function testAddRowWithMoreCellsThanColumns()
    {
        $this->DataTable->addColumn('date');
        $this->DataTable->addColumn('number');

        $this->DataTable->addRow([Carbon::parse('March 24th, 1988'), 12345, 67890]);
    }

    /**
     * @depends testAddColumnWithTypeAndDescription
     * @dataProvider nonCarbonOrDateStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidDateTimeString
     */
    public function testAddRowWithBadDateTypes($badDate)
    {
        $this->DataTable->addColumn('date');

        $this->DataTable->addRow([$badDate]);
    }

    /**
     * @depends testAddColumnWithTypeAndDescription
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidRowProperty
     */
    public function testAddRowWithEmptyArray()
    {
        $this->DataTable->addColumn('date');

        $this->DataTable->addRow([[]]);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAddColumnsWithBadTypesInArray()
    {
        $this->DataTable->addColumns([
            'hotdogs',
            15.6244
        ]);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidColumnType
     */
    public function testAddColumnsWithBadValuesInArray()
    {
        $this->DataTable->addColumns([
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

        $this->DataTable->addColumn('date');

        $this->DataTable->formatColumn('grizzly', $mockDateFormat);
    }

     /**
     * @depends testAddColumnWithTypeAndDescription
     */
    public function testAddRowsWithMultipleColumnsWithDateTimeAndNumbers()
    {
        $this->DataTable->addColumns([
            ['datetime'],
            ['number'],
            ['number']
        ])->addRows([
            [Carbon::parse('March 24th, 1988 8:01:05'), 12345, 67890],
            [Carbon::parse('March 25th, 1988 8:02:06'), 1122, 3344]
        ]);

        $columns = $this->getPrivateProperty($this->DataTable, 'cols');
        $rows    = $this->getPrivateProperty($this->DataTable, 'rows');

        $this->assertEquals('datetime', $columns[0]->getType());
        $this->assertEquals('number', $columns[1]->getType());
        $this->assertEquals('number', $columns[2]->getType());

        $this->assertEquals('Date(1988,2,24,8,1,5)', $rows[0]->getColumnValue(0));
        $this->assertEquals(12345, $rows[0]->getColumnValue(1));
        $this->assertEquals(67890, $rows[0]->getColumnValue(2));

        $this->assertEquals('Date(1988,2,25,8,2,6)', $rows[1]->getColumnValue(0));
        $this->assertEquals(1122, $rows[1]->getColumnValue(1));
        $this->assertEquals(3344, $rows[1]->getColumnValue(2));
    }

    /**
     * @depends testAddColumnWithTypeAndDescription
     */
    public function testGetColumns()
    {
        $this->DataTable->addColumn('date', 'Test1');
        $this->DataTable->addColumn('number', 'Test2');

        $cols = $this->DataTable->getColumns();

        $this->assertEquals('date', $cols[0]->getType());
        $this->assertEquals('number', $cols[1]->getType());
    }

    /**
     * @depends testAddColumnWithType
     * @depends testAddRowWithDate
     * @covers \Khill\Lavacharts\DataTables\DateCell::__toString
     * @covers \Khill\Lavacharts\DataTables\DateCell::__toString
     */
    public function testGetRows()
    {
        $this->DataTable->addColumn('date');

        $this->DataTable->addRow([Carbon::parse('March 24th, 1988')]);
        $this->DataTable->addRow([Carbon::parse('March 25th, 1988')]);

        $rows = $this->DataTable->getRows();

        $this->assertEquals('Date(1988,2,24,0,0,0)', $rows[0]->getColumnValue(0));
        $this->assertEquals('Date(1988,2,25,0,0,0)', $rows[1]->getColumnValue(0));
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
        $this->DataTable->addDateColumn();

        $this->DataTable->addRow(['March 24th, 1988 8:01:05']);

        $row = $this->getPrivateProperty($this->DataTable, 'rows')[0];

        $this->assertEquals('Date(1988,2,24,8,1,5)', $row->getColumnValue(0));
    }

    /**
     * @depends testAddDateColumn
     * @depends testSetDateTimeFormat
     * @depends testAddRowWithDate
     * @covers \Khill\Lavacharts\DataTables\DateCell::parseString
     */
    public function testDateCellParseStringWithFormat()
    {
        $this->DataTable->setDateTimeFormat('d/m/Y')->addDateColumn();

        $this->DataTable->addRows([
            ['11/06/1990'],
            ['12/06/1990']
        ]);

        $rows = $this->getPrivateProperty($this->DataTable, 'rows');

        $this->assertEquals('Date(1990,5,11', substr((string) $rows[0]->getColumnValue(0), 0, 14));
        $this->assertEquals('Date(1990,5,12', substr((string) $rows[1]->getColumnValue(0), 0, 14));
    }

    /**
     * @depends testAddDateColumn
     * @depends testSetDateTimeFormat
     * @depends testAddRowWithDate
     * @expectedException \Khill\Lavacharts\Exceptions\FailedCarbonParsing
     * @covers \Khill\Lavacharts\DataTables\DateCell::parseString
     */
    public function testDateCellParseStringWithBadString()
    {
        $this->DataTable->addDateColumn();

        $this->DataTable->addRows([
            ['11/046/1990'],
            ['12/06/199210']
        ]);
    }

    /**
     * @depends testAddDateColumn
     * @depends testAddRowWithDate
     * @covers \Khill\Lavacharts\DataTables\DateCell::jsonSerialize
     */
    public function testJsonSerializationOfDateCells()
    {
        $this->DataTable->addDateColumn();

        $this->DataTable->addRow([Carbon::parse('March 24th, 1988 8:01:05')]);

        $row = $this->getPrivateProperty($this->DataTable, 'rows')[0];

        $this->assertEquals('"Date(1988,2,24,8,1,5)"', json_encode($row->getColumnValue(0)));
    }
}

