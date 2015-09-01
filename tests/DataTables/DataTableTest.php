<?php

namespace Khill\Lavacharts\Tests\DataTables;

use \Khill\Lavacharts\DataTables\DataTable;
use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Carbon\Carbon;
use \Mockery as m;

class DataTableTest extends ProvidersTestCase
{
    public $DataTable;

    public $columnClassNames = [
        'NumberColumn',
        'StringColumn',
        'DateColumn',
        'DateTimeColumn',
        'TimeOfDayColumn'
    ];

    public $columnTypes = [
        'boolean',
        'number',
        'string',
        'date',
        'datetime',
        'timeofday'
    ];

    public $columnLabels = [
        'Admin',
        'Unique Visitors',
        'People In Group',
        'Most Commits',
        'Entries Edited',
        'Peak Usage Hours'
    ];

    public $tzLA = 'America/Los_Angeles';

    public $tzNY = 'America/New_York';

    public function setUp()
    {
        parent::setUp();

        date_default_timezone_set($this->tzLA);

        $this->DataTable = new DataTable();
    }

    public function columnTypeProvider()
    {
        return array_map(function ($columnType) {
            return [$columnType];
        }, $this->columnTypes);
    }

    public function columnClassProvider()
    {
        return array_map(function ($columnType) {
            return [$columnType];
        }, $this->columnClassNames);
    }

    public function columnTypeAndLabelProvider()
    {
        $columns = [];

        foreach ($this->columnTypes as $index => $type) {
            $columns[] = [$type, $this->columnLabels[$index]];
        }

        return $columns;
    }

    public function getClassName($columnType)
    {
        $namespace = '\\Khill\\Lavacharts\\DataTables\\Columns\\';

        switch ($columnType) {
            case 'boolean':
                return $namespace . 'BooleanColumn';
            case 'string':
                return $namespace . 'StringColumn';
            case 'number':
                return $namespace . 'NumberColumn';
            case 'date':
                return $namespace . 'DateColumn';
            case 'datetime':
                return $namespace . 'DateTimeColumn';
            case 'timeofday':
                return $namespace . 'TimeOfDayColumn';
            default:
                return false;
        }
    }

    public function testDefaultTimezoneUponCreation()
    {
        $tz = $this->getPrivateProperty($this->DataTable, 'timezone');

        $this->assertEquals($this->tzLA, $tz->getName());
    }

    public function testSetTimezoneWithConstructor()
    {
        $datatable = new DataTable($this->tzNY);

        $tz = $this->getPrivateProperty($datatable, 'timezone');

        $this->assertEquals($this->tzNY, $tz->getName());
    }

    public function testSetTimezoneMethod()
    {
        $this->DataTable->setTimezone($this->tzNY);

        $tz = $this->getPrivateProperty($this->DataTable, 'timezone');

        $this->assertEquals($this->tzNY, $tz->getName());
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
        $this->DataTable->setTimezone($this->tzNY);

        $this->assertInstanceOf('DateTimeZone', $this->DataTable->getTimezone());
        $this->assertEquals($this->tzNY, $this->DataTable->getTimezone()->getName());
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

    /**
     * @covers \Khill\Lavacharts\DataTables\DateTable::cell
     */
    public function testStaticallyCreateDataCell()
    {
        $cell = DataTable::cell(5);

        $this->assertInstanceOf('\Khill\Lavacharts\DataTables\DataCell', $cell);
    }

    /**
     * @dataProvider columnTypeProvider
     */
    public function testAddColumnByType($columnType)
    {
        $this->DataTable->addColumn($columnType);

        $column = $this->getPrivateProperty($this->DataTable, 'cols')[0];

        $this->assertInstanceOf($this->getClassName($columnType), $column);
    }

    /**
     * @depends testAddColumnByType
     * @dataProvider columnTypeProvider
     */
    public function testAddColumnByTypeInArray($columnType)
    {
        $this->DataTable->addColumn([$columnType]);

        $column = $this->getPrivateProperty($this->DataTable, 'cols')[0];

        $this->assertInstanceOf($this->getClassName($columnType), $column);
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

    /**
     * @dataProvider columnClassProvider
     * @covers \Khill\Lavacharts\DataTables\DataTable::addBooleanColumn
     * @covers \Khill\Lavacharts\DataTables\DataTable::addStringColumn
     * @covers \Khill\Lavacharts\DataTables\DataTable::addNumberColumn
     * @covers \Khill\Lavacharts\DataTables\DataTable::addDateColumn
     * @covers \Khill\Lavacharts\DataTables\DataTable::addDateTimeColumn
     * @covers \Khill\Lavacharts\DataTables\DataTable::addTimeOfDayColumn
     */
    public function testAddColumnViaNamedAlias($className)
    {
        call_user_func([$this->DataTable, 'add' . $className]);

        $column = $this->getPrivateProperty($this->DataTable, 'cols')[0];

        $this->assertInstanceOf('\\Khill\Lavacharts\\DataTables\\Columns\\' . $className, $column);
    }

    /**
     * @depends testAddColumnByType
     * @depends testAddColumnByTypeInArray
     * @dataProvider columnTypeProvider
     */
    public function testGetColumnTypeWithIndex($type)
    {
        $this->DataTable->addColumn($type);

        $this->assertEquals($type, $this->DataTable->getColumnType(0));
    }

    /**
     * @depends testAddColumnViaNamedAlias
     */
    public function testDropColumnWithIndex()
    {
        $this->DataTable->addDateColumn();
        $this->DataTable->addNumberColumn();
        $this->DataTable->addStringColumn();

        $columns = $this->getPrivateProperty($this->DataTable, 'cols');

        $this->assertEquals(3, count($columns));
        $this->assertEquals('number', $columns[1]->getType());

        $this->DataTable->dropColumn(1);
        $columns = $this->getPrivateProperty($this->DataTable, 'cols');
        $this->assertEquals(2, count($columns));
        $this->assertEquals('string', $columns[1]->getType());

        $this->DataTable->dropColumn(1);
        $columns = $this->getPrivateProperty($this->DataTable, 'cols');
        $this->assertEquals(1, count($columns));
        $this->assertFalse(isset($columns[1]));
    }

    /**
     * @depends testAddColumnViaNamedAlias
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidColumnIndex
     */
    public function testDropColumnWithBadType($badVals)
    {
        $this->DataTable->addNumberColumn();

        $this->DataTable->dropColumn($badVals);
    }

    /**
     * @depends testAddColumnViaNamedAlias
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidColumnIndex
     */
    public function testDropColumnWithNonExistentIndex()
    {
        $this->DataTable->addNumberColumn();
        $this->DataTable->addNumberColumn();
        $this->DataTable->addNumberColumn();

        $this->DataTable->dropColumn(4);
    }

    /**
     * @depends testAddColumnByType
     * @dataProvider columnTypeAndLabelProvider
     */
    public function testAddColumnWithTypeAndLabel($columnType, $columnLabel)
    {
        $this->DataTable->addColumn($columnType, $columnLabel);

        $column = $this->getPrivateProperty($this->DataTable, 'cols')[0];

        $this->assertEquals($columnType, $column->getType());
        $this->assertEquals($columnLabel, $column->getLabel());
    }

    public function testAddColumnWithArrayOfTypeAndLabel()
    {
        $this->DataTable->addColumn(['date', 'Days in March']);

        $column = $this->getPrivateProperty($this->DataTable, 'cols')[0];

        $this->assertEquals('date', $column->getType());
        $this->assertEquals('Days in March', $column->getLabel());
    }

    public function testAddColumnsWithArrayOfTypeAndLabel()
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
     * @depends testAddColumnViaNamedAlias
     */
    public function testAddRowWithEmptyArrayForNull()
    {
        $this->DataTable->addDateColumn();
        $this->DataTable->addRow([]);

        $row = $this->getPrivateProperty($this->DataTable, 'rows')[0];

        $this->assertEquals(null, $row->getColumnValue(0));
    }

    /**
     * @depends testAddColumnViaNamedAlias
     */
    public function testAddRowWithNull()
    {
        $this->DataTable->addDateColumn();
        $this->DataTable->addRow(null);

        $row = $this->getPrivateProperty($this->DataTable, 'rows')[0];

        $this->assertEquals(null, $row->getColumnValue(0));
    }

    /**
     * @depends testAddColumnViaNamedAlias
     */
    public function testAddRowWithDate()
    {
        $this->DataTable->addDateColumn();
        $this->DataTable->addRow([Carbon::parse('March 24th, 1988')]);

        $column = $this->getPrivateProperty($this->DataTable, 'cols')[0];
        $row    = $this->getPrivateProperty($this->DataTable, 'rows')[0];

        $this->assertEquals('date', $column->getType());
        $this->assertEquals('Date(1988,2,24,0,0,0)', $row->getColumnValue(0));
    }

    /**
     * @depends testAddColumnViaNamedAlias
     */
    public function testAddRowWithMultipleColumnsWithDateAndNumbers()
    {
        $this->DataTable->addDateColumn();
        $this->DataTable->addNumberColumn();
        $this->DataTable->addNumberColumn();

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
     * @depends testAddColumnViaNamedAlias
     */
    public function testAddRowsWithMultipleColumnsWithDateAndNumbers()
    {
        $this->DataTable->addDateColumn();
        $this->DataTable->addNumberColumn();
        $this->DataTable->addNumberColumn();

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
     * @depends testAddColumnViaNamedAlias
     * @depends testAddRowsWithMultipleColumnsWithDateAndNumbers
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidRowDefinition
     */
    public function testAddRowsWithBadColumnValue()
    {
        $this->DataTable->addDateColumn();
        $this->DataTable->addNumberColumn();

        $rows = [
            [Carbon::parse('March 24th, 1988'), 12345],
            234.234
        ];

        $this->DataTable->addRows($rows);
    }

    /**
     * @depends testAddColumnViaNamedAlias
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidCellCount
     */
    public function testAddRowWithMoreCellsThanColumns()
    {
        $this->DataTable->addDateColumn();
        $this->DataTable->addNumberColumn();

        $this->DataTable->addRow([Carbon::parse('March 24th, 1988'), 12345, 67890]);
    }

    /**
     * @depends testAddColumnViaNamedAlias
     * @dataProvider nonCarbonOrDateStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidDateTimeString
     */
    public function testAddRowWithBadDateTypes($badDate)
    {
        $this->DataTable->addDateColumn();

        $this->DataTable->addRow([$badDate]);
    }

    /**
     * @depends testAddColumnViaNamedAlias
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidRowProperty
     */
    public function testAddRowWithEmptyArray()
    {
        $this->DataTable->addDateColumn();

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
     * @depends testAddColumnViaNamedAlias
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidColumnIndex
     */
    public function testFormatColumnWithBadColumnLabel()
    {
        $mockDateFormat = m::mock('Khill\Lavacharts\DataTables\Formats\DateFormat');

        $this->DataTable->addDateColumn();

        $this->DataTable->formatColumn('grizzly', $mockDateFormat);
    }

     /**
     * @depends testAddColumnWithTypeAndLabel
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
     * @depends testAddColumnWithTypeAndLabel
     */
    public function testGetColumnWithIndex()
    {
        $this->DataTable->addColumn('date', 'Test1');
        $this->DataTable->addColumn('number', 'Test2');

        $column = $this->DataTable->getColumn(1);

        $this->assertInstanceOf('\Khill\Lavacharts\DataTables\Columns\NumberColumn', $column);
    }

    /**
     * @depends testAddColumnWithTypeAndLabel
     */
    public function testGetColumns()
    {
        $this->DataTable->addColumn('date', 'Test1');
        $this->DataTable->addColumn('number', 'Test2');

        $columns = $this->DataTable->getColumns();

        $this->assertInstanceOf('\Khill\Lavacharts\DataTables\Columns\DateColumn', $columns[0]);
        $this->assertInstanceOf('\Khill\Lavacharts\DataTables\Columns\NumberColumn', $columns[1]);
    }

    /**
     * @depends testAddColumnViaNamedAlias
     * @depends testAddRowWithDate
     * @covers \Khill\Lavacharts\DataTables\DateCell::__toString
     * @covers \Khill\Lavacharts\DataTables\DateCell::__toString
     */
    public function testGetRows()
    {
        $this->DataTable->addDateColumn();

        $this->DataTable->addRow([Carbon::parse('March 24th, 1988')]);
        $this->DataTable->addRow([Carbon::parse('March 25th, 1988')]);

        $rows = $this->DataTable->getRows();

        $this->assertEquals('Date(1988,2,24,0,0,0)', $rows[0]->getColumnValue(0));
        $this->assertEquals('Date(1988,2,25,0,0,0)', $rows[1]->getColumnValue(0));
    }

    /**
     * @depends testAddColumnViaNamedAlias
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
     * @depends testAddColumnViaNamedAlias
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
     * @depends testAddColumnViaNamedAlias
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
     * @depends testAddColumnViaNamedAlias
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



    /********************************************************************************************
     * Tests
     ********************************************************************************************/
}



