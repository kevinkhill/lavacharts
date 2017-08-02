<?php

namespace Khill\Lavacharts\Tests\DataTables;

use Carbon\Carbon;
use Khill\Lavacharts\DataTables\Cells\DateCell;
use Khill\Lavacharts\DataTables\Columns\Column;
use Khill\Lavacharts\DataTables\Columns\Format;
use Khill\Lavacharts\DataTables\DataTable;
use Khill\Lavacharts\DataTables\Row;
use Khill\Lavacharts\Tests\ProvidersTestCase;
use Mockery;

class DataTableTest extends ProvidersTestCase
{
    const TIMEZONE_LA = 'America/Los_Angeles';

    const TIMEZONE_NY = 'America/New_York';

    public $columnTypes = [
        'BooleanColumn',
        'NumberColumn',
        'StringColumn',
        'DateColumn',
        'DateTimeColumn',
        'TimeOfDayColumn'
    ];

    public $columnLabels = [
        'tooltip',
        'Admin',
        'Unique Visitors',
        'People In Group',
        'Most Commits',
        'Entries Edited',
        'Peak Usage Hours'
    ];

    /**
     * @var DataTable
     */
    private $datatable;

    public function setUp()
    {
        parent::setUp();

        date_default_timezone_set(self::TIMEZONE_LA);

        $this->datatable = new DataTable();

        $this->mockDateFormat = Mockery::mock(Format::class)
            ->shouldReceive('setOptions')
            ->with([])
            ->zeroOrMoreTimes()
            ->getMock();

        $this->mockNumberFormat = Mockery::mock(Format::class)
            ->shouldReceive('setOptions')
            ->with([])
            ->zeroOrMoreTimes()
            ->getMock();
    }

    public function columnCreationNameProvider()
    {
        return array_map(function ($columnName) {
            return [$columnName];
        }, $this->columnTypes);
    }

    public function columnTypeAndLabelProvider()
    {
        $columns = [];

        foreach (Column::TYPES as $index => $type) {
            $columns[] = [$type, $this->columnLabels[$index]];
        }

        return $columns;
    }

    public function testDefaultTimezoneUponCreation()
    {
        $datatable = new DataTable();

        $this->assertEquals(self::TIMEZONE_LA, $datatable->getOption('timezone'));
    }

    public function testSetTimezoneWithConstructorOptions()
    {
        $datatable = new DataTable([
            'timezone' => self::TIMEZONE_NY
        ]);

        $this->assertEquals(self::TIMEZONE_NY, $datatable->getOption('timezone'));
    }

    /**
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testDefaultUsedIfSettingTimezoneInConstructorWithBadType()
    {
        $datatable = new DataTable([
            'timezone' => 1
        ]);

        $this->assertNotEquals(1, $datatable->getOption('timezone'));
        $this->assertEquals(self::TIMEZONE_LA, $datatable->getOption('timezone'));
    }

//TODO: map set option calls to set methods if available.
//    public function testSetTimeZoneMethod()
//    {
//        $this->datatable->setOption('timezone', self::TIMEZONE_NY);
//
//        $this->assertEquals(self::TIMEZONE_NY, $this->datatable->getOption('timezone'));
//    }

    public function testSetTimezoneOption()
    {
        $this->datatable->setOption('timezone', self::TIMEZONE_NY);

        $this->assertEquals(self::TIMEZONE_NY, $this->datatable->getOption('timezone'));
    }

    /**
     * @expectedException \Exception
     */
    public function testSetTimezoneWithInvalidTimezone()
    {
        $this->datatable->setTimezone('Jupiter/Europa');
    }

    /**
     * @depends testSetTimezoneOption
     */
    public function testGetTimezoneOption()
    {
        $this->datatable->setOption('datetime_format', 'YYYY-mm-dd');

        $this->assertEquals('YYYY-mm-dd', $this->datatable->getOption('datetime_format'));
    }

//TODO: allow option fetching with __get()?
//    public function testGetTimezoneMethod()
//    {
//        $this->datatable->setTimezone(self::TIMEZONE_NY);
//
//        $this->assertInstanceOf('DateTimeZone', $this->datatable->getTimezone());
//        $this->assertEquals(self::TIMEZONE_NY, $this->datatable->getTimezone()->getName());
//    }

    public function testSettingDateTimeFormatWithConstructorOptions()
    {
        $datatable = new DataTable([
            'datetime_format' => 'YYYY-mm-dd'
        ]);

        $this->assertEquals('YYYY-mm-dd', $datatable->getOption('datetime_format'));
    }

    public function testSetDateTimeFormatOption()
    {
        $this->datatable->setOption('datetime_format', 'YYYY-mm-dd');

        $this->assertEquals('YYYY-mm-dd', $this->datatable->getOption('datetime_format'));
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidDateTimeFormat
     */
    public function testSetDateTimeFormatWithBadTypes()
    {
        $this->datatable->setDateTimeFormat(1);
    }

    public function testGetDateTimeFormat()
    {
        $this->datatable->setDateTimeFormat('YYYY-mm-dd');

        $this->assertEquals('YYYY-mm-dd', $this->datatable->getOption('datetime_format'));
    }

    /**
     * @dataProvider columnTypeProvider
     */
    public function testAddColumnByType($columnType)
    {
        $this->datatable->addColumn($columnType);

        $column = $this->datatable->getColumn(0);

        $this->assertEquals($columnType, $column->getType());
    }

    /**
     * @dataProvider columnTypeProvider
     */
    public function testAddColumnByTypeInArray($columnType)
    {
        $this->datatable->addColumn([$columnType]);

        $column = $this->datatable->getColumn(0);

        $this->assertEquals($columnType, $column->getType());
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidArgumentException
     */
    public function testAddColumnWithBadType()
    {
        $this->datatable->addColumn(1);
    }

    /**
     * @dataProvider columnCreationNameProvider
     * @covers \Khill\Lavacharts\DataTables\DataTable::addBooleanColumn
     * @covers \Khill\Lavacharts\DataTables\DataTable::addStringColumn
     * @covers \Khill\Lavacharts\DataTables\DataTable::addNumberColumn
     * @covers \Khill\Lavacharts\DataTables\DataTable::addDateColumn
     * @covers \Khill\Lavacharts\DataTables\DataTable::addDateTimeColumn
     * @covers \Khill\Lavacharts\DataTables\DataTable::addTimeOfDayColumn
     */
    public function testAddColumnViaNamedAlias($columnType)
    {
        call_user_func([$this->datatable, 'add' . $columnType]);

        $column = $this->datatable->getColumn(0);

        $type = strtolower(str_replace('Column', '', $columnType));

        $this->assertEquals($type, $column->getType());
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidColumnDefinition
     * @covers \Khill\Lavacharts\DataTables\DataTable::addColumns
     */
    public function testAddColumnsWithBadTypesInArray()
    {
        $this->datatable->addColumns([
            5.6,
            15.6244,
            'hotdogs'
        ]);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidColumnDefinition
     * @covers \Khill\Lavacharts\DataTables\DataTable::addColumns
     */
    public function testAddColumnsWithBadValuesInArray()
    {
        $this->datatable->addColumns([
            'unicorn',
            'tacos'
        ]);
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\DataTable::addRoleColumn
     */
    public function testAddRoleColumn()
    {
        $this->datatable->addRoleColumn('number', 'interval');

        $column = $this->datatable->getColumn(0);

        $this->assertEquals('number', $column->getType());
        $this->assertEquals('interval', $column->getRole());
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\DataTable::addRoleColumn
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidColumnType
     */
    public function testAddRoleColumnWithBadColumnTypes()
    {
        $this->datatable->addRoleColumn('taco', 'interval');
    }

    /**
     * @dataProvider nonStringProvider
     * @covers \Khill\Lavacharts\DataTables\DataTable::addRoleColumn
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidColumnRole
     */
    public function testAddRoleColumnWithBadRoleTypes($badTypes)
    {
        $this->datatable->addRoleColumn('number', $badTypes);
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\DataTable::addRoleColumn
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidColumnRole
     */
    public function testAddRoleColumnWithBadRoleValue()
    {
        $this->datatable->addRoleColumn('number', 'stairs');
    }

    public function testDropColumnWithIndex()
    {
        $this->datatable->addDateColumn();
        $this->datatable->addNumberColumn();
        $this->datatable->addStringColumn();

        $columns = $this->datatable->getColumns();

        $this->assertEquals(3, count($columns));
        $this->assertEquals('number', $columns[1]->getType());

        $this->datatable->dropColumn(1);
        $columns = $this->datatable->getColumns();
        $this->assertEquals(2, count($columns));
        $this->assertEquals('string', $columns[1]->getType());

        $this->datatable->dropColumn(1);
        $columns = $this->datatable->getColumns();
        $this->assertEquals(1, count($columns));
        $this->assertFalse(isset($columns[1]));
    }

    /**
     * @depends testAddColumnViaNamedAlias
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidColumnIndex
     */
    public function testDropColumnWithBadType($badTypes)
    {
        $this->datatable->addNumberColumn();

        $this->datatable->dropColumn($badTypes);
    }

    /**
     * @depends testAddColumnViaNamedAlias
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidColumnIndex
     * @covers \Khill\Lavacharts\DataTables\DataTable::dropColumn
     */
    public function testDropColumnWithNonExistentIndex()
    {
        $this->datatable->addNumberColumn();
        $this->datatable->addNumberColumn();
        $this->datatable->addNumberColumn();

        $this->datatable->dropColumn(4);
    }

    /**
     * @depends testAddColumnByType
     * @dataProvider columnTypeAndLabelProvider
     * @covers \Khill\Lavacharts\DataTables\DataTable::addColumn
     */
    public function testAddColumnWithTypeAndLabel($columnType, $columnLabel)
    {
        $this->datatable->addColumn($columnType, $columnLabel);

        $column = $this->datatable->getColumn(0);

        $this->assertEquals($columnType, $column->getType());
        $this->assertEquals($columnLabel, $column->getLabel());
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\DataTable::addColumn
     */
    public function testAddColumnWithArrayOfTypeAndLabel()
    {
        $this->datatable->addColumn(['date', 'Days in March']);

        $column = $this->datatable->getColumn(0);

        $this->assertEquals('date', $column->getType());
        $this->assertEquals('Days in March', $column->getLabel());
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\DataTable::addColumns
     */
    public function testAddColumnsWithArrayOfTypeAndLabel()
    {
        $this->datatable->addColumns([
            ['date', 'Days in March'],
            ['number', 'Day of the Week'],
            ['number', 'Temperature'],
        ]);

        $columns = $this->datatable->getColumns();

        $this->assertEquals('date', $columns[0]->getType());
        $this->assertEquals('Days in March', $columns[0]->getLabel());

        $this->assertEquals('number', $columns[1]->getType());
        $this->assertEquals('Day of the Week', $columns[1]->getLabel());

        $this->assertEquals('number', $columns[2]->getType());
        $this->assertEquals('Temperature', $columns[2]->getLabel());
    }

    /**
     * @depends testAddColumnViaNamedAlias
     * @covers \Khill\Lavacharts\DataTables\DataTable::addRow
     */
//    public function testAddRowWithEmptyArrayForNull()
//    {
//        $this->datatable->addDateColumn();
//        $this->datatable->addRow([]);
//
//        $row = $this->datatable->getRow(0);
//
//        $this->assertNull($this->inspect($row, 'cells')[0]->getValue());
//    }

    /**
     * @depends testAddColumnViaNamedAlias
     * @covers \Khill\Lavacharts\DataTables\DataTable::addRow
     */
//    public function testAddRowWithNull()
//    {
//        $this->datatable->addDateColumn();
//        $this->datatable->addRow(null);
//
//        $row = $this->datatable->getRow(0);
//
//        $this->assertNull($this->inspect($row, 'cells')[0]->getValue());
//    }

    /**
     * @covers \Khill\Lavacharts\DataTables\DataTable::addRow
     */
    public function testAddRowWithDate()
    {
        $this->datatable->addDateColumn();
        $this->datatable->addRow([Carbon::parse('March 24th, 1988')]);

        $cell = $this->datatable->getCell(0, 0);

        $this->assertInstanceOf(DateCell::class, $cell);
        $this->assertEquals('Date(1988,2,24,0,0,0)', (string) $cell);
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\DataTable::addRow
     */
    public function testAddRowWithMultipleColumnsWithDateAndNumbers()
    {
        $this->datatable->addDateColumn();
        $this->datatable->addNumberColumn();
        $this->datatable->addNumberColumn();

        $this->datatable->addRow([Carbon::parse('March 24th, 1988'), 12345, 67890]);

        $columns = $this->datatable->getColumns();
        $row     = $this->datatable->getRow(0);

        $this->assertEquals('date', $columns[0]->getType());
        $this->assertEquals('number', $columns[1]->getType());
        $this->assertEquals('number', $columns[2]->getType());

        $this->assertEquals('Date(1988,2,24,0,0,0)', $row->getCell(0));
        $this->assertEquals(12345, $row->getCell(1)->getValue());
        $this->assertEquals(67890, $row->getCell(2)->getValue());
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\DataTable::addRows
     */
    public function testAddRowsWithMultipleColumnsWithDateAndNumbers()
    {
        $this->datatable->addDateColumn();
        $this->datatable->addNumberColumn();
        $this->datatable->addNumberColumn();

        $rows = [
            [Carbon::parse('March 24th, 1988'), 12345, 67890],
            [Carbon::parse('March 25th, 1988'), 1122, 3344]
        ];

        $this->datatable->addRows($rows);

        $columns = $this->datatable->getColumns();
        $rows    = $this->datatable->getRows();

        $this->assertEquals('date', $columns[0]->getType());
        $this->assertEquals('number', $columns[1]->getType());
        $this->assertEquals('number', $columns[2]->getType());

        $this->assertEquals('Date(1988,2,24,0,0,0)', (string) $rows[0]->getCell(0));
        $this->assertEquals(12345, $rows[0]->getCell(1)->getValue());
        $this->assertEquals(67890, $rows[0]->getCell(2)->getValue());

        $this->assertEquals('Date(1988,2,25,0,0,0)', (string) $rows[1]->getCell(0));
        $this->assertEquals(1122, $rows[1]->getCell(1)->getValue());
        $this->assertEquals(3344, $rows[1]->getCell(2)->getValue());
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidCellCount
     * @covers \Khill\Lavacharts\DataTables\DataTable::addRow
     */
    public function testAddRowWithMoreCellsThanColumns()
    {
        $this->datatable->addDateColumn();
        $this->datatable->addNumberColumn();

        $this->datatable->addRow([Carbon::parse('March 24th, 1988'), 12345, 67890]);
    }

    /**
     * @dataProvider nonCarbonOrDateStringProvider
     * @expectedException \Exception
     * @covers \Khill\Lavacharts\DataTables\DataTable::addRow
     */
    public function testAddRowWithBadDateTypes($badDate)
    {
        $this->datatable->addDateColumn();

        $this->datatable->addRow([$badDate]);
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\DataTable::addRow
     */
    public function testAddRowWithEmptyArray()
    {
        $this->datatable->addDateColumn();

        $this->datatable->addRow([]);
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\DataTable::getRows
     */
    public function testGetRows()
    {
        $this->datatable->addDateColumn();
        $this->datatable->addNumberColumn();
        $this->datatable->addNumberColumn();

        $rows = [
            [Carbon::parse('March 24th, 1988'), 12345, 67890],
            [Carbon::parse('March 25th, 1988'), 1122, 3344]
        ];

        $this->datatable->addRows($rows);

        $rows = $this->datatable->getRows();

        $this->assertInstanceOf(Row::class, $rows[0]);
        $this->assertInstanceOf(Row::class, $rows[1]);
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\DataTable::getRowCount
     */
    public function testGetRowCount()
    {
        $this->datatable->addDateColumn();
        $this->datatable->addNumberColumn();
        $this->datatable->addNumberColumn();

        $rows = [
            [Carbon::parse('March 24th, 1988'), 12345, 67890],
            [Carbon::parse('March 25th, 1988'), 1122, 3344]
        ];

        $this->datatable->addRows($rows);

        $this->assertEquals(2, $this->datatable->getRowCount());
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\DataTable::formatColumn
     */
    public function testFormatColumn()
    {
        $this->datatable->addDateColumn();

        $this->datatable->formatColumn(0, $this->mockDateFormat);

        $column = $this->datatable->getColumn(0);

        $this->assertInstanceOf(
            Format::class,
            $this->inspect($column, 'format')
        );
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidColumnIndex
     * @covers \Khill\Lavacharts\DataTables\DataTable::formatColumn
     */
    public function testFormatColumnWithBadIndex()
    {
        $this->datatable->addDateColumn();

        $this->datatable->formatColumn(672, $this->mockDateFormat);
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\DataTable::formatColumns
     */
    public function testFormatColumns()
    {
        $this->datatable->addDateColumn();
        $this->datatable->addNumberColumn();
        $this->datatable->addNumberColumn();

        $this->datatable->formatColumns([
            0 => $this->mockDateFormat,
            2 => $this->mockNumberFormat
        ]);

        $columns = $this->datatable->getColumns();

        $this->assertInstanceOf(
            Format::class,
            $this->inspect($columns[0], 'format')
        );

        $this->assertInstanceOf(
            Format::class,
            $this->inspect($columns[2], 'format')
        );
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\DataTable::getFormattedColumns
     */
    public function testGetFormattedColumns()
    {
        $this->mockDateFormat
             ->shouldReceive('setIndex')
             ->with(0)
             ->once();

        $this->mockNumberFormat
            ->shouldReceive('setIndex')
            ->with(2)
            ->once();

        $this->datatable->addDateColumn();
        $this->datatable->addNumberColumn();
        $this->datatable->addNumberColumn();
        $this->datatable->addNumberColumn();

        $this->datatable->formatColumns([
            0 => $this->mockDateFormat,
            2 => $this->mockNumberFormat
        ]);

        $columns = $this->datatable->getFormattedColumns();

        $this->assertInstanceOf(
            Format::class,
            $this->inspect($columns[0], 'format')
        );

        $this->assertInstanceOf(
            Format::class,
            $this->inspect($columns[2], 'format')
        );
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\DataTable::hasFormattedColumns
     */
    public function testHasFormattedColumns()
    {
        $this->mockDateFormat
            ->shouldReceive('setIndex')
            ->with(0)
            ->once();

        $this->mockNumberFormat
            ->shouldReceive('setIndex')
            ->with(2)
            ->once();

        $this->datatable->addDateColumn();
        $this->datatable->addNumberColumn();
        $this->datatable->addNumberColumn();
        $this->datatable->addNumberColumn();

        $this->datatable->formatColumns([
            0 => $this->mockDateFormat,
            2 => $this->mockNumberFormat
        ]);

        $this->assertTrue($this->datatable->hasFormattedColumns());
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\DataTable::hasFormattedColumns
     */
    public function testHasFormattedColumnsWithNoFormattedColumns()
    {
        $this->datatable->addDateColumn();
        $this->datatable->addNumberColumn();
        $this->datatable->addNumberColumn();
        $this->datatable->addNumberColumn();

        $this->assertFalse($this->datatable->hasFormattedColumns());
    }

    public function testAddRowsWithMultipleColumnsWithDateTimeAndNumbers()
    {
        $this->datatable->addColumns([
            ['datetime'],
            ['number'],
            ['number']
        ])->addRows([
            [Carbon::parse('March 24th, 1988 8:01:05'), 12345, 67890],
            [Carbon::parse('March 25th, 1988 8:02:06'), 1122, 3344]
        ]);

        $columns = $this->datatable->getColumns();
        $rows    = $this->datatable->getRows();

        $this->assertEquals('datetime', $columns[0]->getType());
        $this->assertEquals('number', $columns[1]->getType());
        $this->assertEquals('number', $columns[2]->getType());

        $this->assertEquals('Date(1988,2,24,8,1,5)', $rows[0]->getCell(0));
        $this->assertEquals(12345, $rows[0]->getCell(1)->getValue());
        $this->assertEquals(67890, $rows[0]->getCell(2)->getValue());

        $this->assertEquals('Date(1988,2,25,8,2,6)', $rows[1]->getCell(0));
        $this->assertEquals(1122, $rows[1]->getCell(1)->getValue());
        $this->assertEquals(3344, $rows[1]->getCell(2)->getValue());
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\DataTable::getColumn
     */
    public function testGetColumn()
    {
        $this->datatable->addColumn('date', 'Test1');
        $this->datatable->addColumn('number', 'Test2');

        $column = $this->datatable->getColumn(1);

        $this->assertInstanceOf(Column::class, $column);
        $this->assertEquals('Test2', $this->inspect($column, 'label'));
    }

    public function testGetColumns()
    {
        $this->datatable->addColumn('date', 'Test1');
        $this->datatable->addColumn('number', 'Test2');

        $columns = $this->datatable->getColumns();

        $this->assertTrue(is_array($columns));
        $this->assertInstanceOf(Column::class, $columns[0]);
        $this->assertInstanceOf(Column::class, $columns[1]);
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\DataTable::getColumnLabel
     */
    public function testGetColumnLabel()
    {
        $this->datatable->addColumn('date', 'Test1');
        $this->datatable->addColumn('number', 'Test2');

        $this->assertEquals('Test2', $this->datatable->getColumnLabel(1));
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\DataTable::getColumnLabels
     */
    public function testGetColumnLabels()
    {
        $this->datatable->addColumn('date', 'Test1');
        $this->datatable->addColumn('number', 'Test2');

        $labels = $this->datatable->getColumnLabels();

        $this->assertTrue(is_array($labels));
        $this->assertEquals('Test1', $labels[0]);
        $this->assertEquals('Test2', $labels[1]);
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\DataTable::getColumnType
     */
    public function testGetColumnType()
    {
        $this->datatable->addColumn('date', 'Test1');
        $this->datatable->addColumn('number', 'Test2');

        $this->assertEquals('date', $this->datatable->getColumnType(0));
    }

    /**
     * @dataProvider columnTypeProvider
     */
    public function testGetColumnTypeWithIndex($type)
    {
        $this->datatable->addColumn($type);

        $this->assertEquals($type, $this->datatable->getColumnType(0));
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\DataTable::getColumnsByType
     */
    public function testGetColumnsByType()
    {
        $this->datatable->addDateColumn();
        $this->datatable->addNumberColumn();

        $this->assertEquals([0], array_keys($this->datatable->getColumnsByType('date')));
        $this->assertEquals([1], array_keys($this->datatable->getColumnsByType('number')));
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\DataTable::getColumnsByType
     */
    public function testGetColumnsByTypeWithDuplicateTypes()
    {
        $this->datatable->addDateColumn();
        $this->datatable->addNumberColumn();
        $this->datatable->addNumberColumn();

        $this->assertTrue(is_array($this->datatable->getColumnsByType('number')));
        $this->assertEquals([1,2], array_keys($this->datatable->getColumnsByType('number')));
    }

    public function testBare()
    {
        $this->datatable->addNumberColumn('num')->addRows([[1],[2],[3],[4]]);

        $this->assertEquals(4, $this->datatable->getRowCount());

        $bareTable = $this->datatable->bare();

        $this->assertEquals(0, $bareTable->getRowCount());
    }
}
