<?php namespace Lavacharts\Tests\Configs;

use \Lavacharts\Configs\DataTable;
use \Carbon\Carbon;

class DataTableTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->dt = new DataTable;
    }
    
    public function testGetColumns()
    {
        $this->dt->addColumn('date', 'Test1');
        $this->dt->addColumn('number', 'Test2');

        $cols = $this->dt->getColumns();

        $this->assertEquals($cols[0]['type'], 'date');
        $this->assertEquals($cols[1]['type'], 'number');
    }

    public function testGetRows()
    {
        $this->dt->addColumn('date');

        $this->dt->addRow(array(Carbon::parse('March 24th, 1988')));
        $this->dt->addRow(array(Carbon::parse('March 25th, 1988')));

        $rows = $this->dt->getRows();

        $this->assertEquals($rows[0]['c'][0]['v'], 'Date(1988, 2, 24, 0, 0, 0)');
        $this->assertEquals($rows[1]['c'][0]['v'], 'Date(1988, 2, 25, 0, 0, 0)');
    }

    /**
     * @depends testGetColumns
     */
    public function testAddColumnWithTypeOnly()
    {
        $this->dt->addColumn('date');

        $cols = $this->dt->getColumns();

        $this->assertEquals($cols[0]['type'], 'date');
    }

    /**
     * @depends testGetColumns
     */
    public function testAddColumnWithArrayOfTypeOnly()
    {
        $this->dt->addColumn(array('date'));

        $cols = $this->dt->getColumns();

        $this->assertEquals($cols[0]['type'], 'date');
    }

    /**
     * @depends testGetColumns
     */
    public function testAddColumnWithTypeAndDescription()
    {
        $this->dt->addColumn('date', 'Days in March');

        $cols = $this->dt->getColumns();

        $this->assertEquals($cols[0]['type'],  'date');
        $this->assertEquals($cols[0]['label'], 'Days in March');
    }

    /**
     * @depends testGetColumns
     */
    public function testAddColumnWithArrafOfTypeAndDescription()
    {
        $this->dt->addColumn(array('date', 'Days in March'));

        $cols = $this->dt->getColumns();

        $this->assertEquals($cols[0]['type'],  'date');
        $this->assertEquals($cols[0]['label'], 'Days in March');
    }

    /**
     * @depends testGetColumns
     */
    public function testAddColumnWithTypeAndDescriptionAndId()
    {
        $this->dt->addColumn('date', 'Days in March', 'march-dates');

        $cols = $this->dt->getColumns();

        $this->assertEquals($cols[0]['type'],  'date');
        $this->assertEquals($cols[0]['label'], 'Days in March');
        $this->assertEquals($cols[0]['id'],    'march-dates');
    }

    /**
     * @depends testGetColumns
     */
    public function testAddColumnWithArrayOfTypeAndDescriptionAndId()
    {
        $this->dt->addColumn(array('date', 'Days in March', 'march-dates'));

        $cols = $this->dt->getColumns();

        $this->assertEquals($cols[0]['type'],  'date');
        $this->assertEquals($cols[0]['label'], 'Days in March');
        $this->assertEquals($cols[0]['id'],    'march-dates');
    }

    /**
     * @depends testGetColumns
     * @depends testAddColumnWithArrayOfTypeAndDescriptionAndId
     */
    public function testAddMultipleColumnsWithArrayOfTypeAndDescriptionAndId()
    {
        $this->dt->addColumns(array(
            array('date', 'Days in March', 'march-dates'),
            array('number', 'Day of the Week', 'dotw'),
            array('number', 'Temperature', 'temp'),
        ));

        $cols = $this->dt->getColumns();

        $this->assertEquals($cols[0]['type'],  'date');
        $this->assertEquals($cols[0]['label'], 'Days in March');
        $this->assertEquals($cols[0]['id'],    'march-dates');

        $this->assertEquals($cols[1]['type'],  'number');
        $this->assertEquals($cols[1]['label'], 'Day of the Week');
        $this->assertEquals($cols[1]['id'],    'dotw');

        $this->assertEquals($cols[2]['type'],  'number');
        $this->assertEquals($cols[2]['label'], 'Temperature');
        $this->assertEquals($cols[2]['id'],    'temp');
    }

    /**
     * @depends testGetColumns
     * @depends testGetRows
     * @depends testAddColumnWithTypeOnly
     */
    public function testAddRowWithTypeDateOnly()
    {
        $this->dt->addColumn('date');

        $this->dt->addRow(array(Carbon::parse('March 24th, 1988')));

        $cols = $this->dt->getColumns();
        $rows = $this->dt->getRows();

        $this->assertEquals($cols[0]['type'], 'date');
        $this->assertEquals($rows[0]['c'][0]['v'], 'Date(1988, 2, 24, 0, 0, 0)');
    }

    /**
     * @depends testGetColumns
     * @depends testGetRows
     * @depends testAddColumnWithTypeAndDescription
     */
    public function testAddRowWithMultipleColumnsWithDateAndNumbers()
    {
        $this->dt->addColumn('date');
        $this->dt->addColumn('number');
        $this->dt->addColumn('number');

        $this->dt->addRow(array(Carbon::parse('March 24th, 1988'), 12345, 67890));

        $cols = $this->dt->getColumns();
        $rows = $this->dt->getRows();

        $this->assertEquals($cols[0]['type'], 'date');
        $this->assertEquals($cols[1]['type'], 'number');
        $this->assertEquals($rows[0]['c'][0]['v'], 'Date(1988, 2, 24, 0, 0, 0)');
        $this->assertEquals($rows[0]['c'][1]['v'], 12345);
        $this->assertEquals($rows[0]['c'][2]['v'], 67890);
    }

    /**
     * @depends testGetColumns
     * @depends testGetRows
     * @depends testAddColumnWithTypeAndDescription
     */
    public function testAddMultipleRowsWithMultipleColumnsWithDateAndNumbers()
    {
        $this->dt->addColumn('date');
        $this->dt->addColumn('number');
        $this->dt->addColumn('number');

        $rows = array(
            array(Carbon::parse('March 24th, 1988'), 12345, 67890),
            array(Carbon::parse('March 25th, 1988'), 1122, 3344)
        );

        $this->dt->addRows($rows);

        $cols = $this->dt->getColumns();
        $rows = $this->dt->getRows();

        $this->assertEquals($cols[0]['type'], 'date');
        $this->assertEquals($cols[1]['type'], 'number');
        $this->assertEquals($rows[0]['c'][0]['v'], 'Date(1988, 2, 24, 0, 0, 0)');
        $this->assertEquals($rows[0]['c'][1]['v'], 12345);
        $this->assertEquals($rows[0]['c'][2]['v'], 67890);
        $this->assertEquals($rows[1]['c'][0]['v'], 'Date(1988, 2, 25, 0, 0, 0)');
        $this->assertEquals($rows[1]['c'][1]['v'], 1122);
        $this->assertEquals($rows[1]['c'][2]['v'], 3344);
    }

    /**
     * @depends testGetColumns
     * @depends testGetRows
     * @depends testAddColumnWithTypeAndDescription
     * @expectedException Lavacharts\Exceptions\InvalidRowDefinition
     */
    public function testAddBadMultipleRowsWithMultipleColumnsWithDateAndNumbers()
    {
        $this->dt->addColumn('date');
        $this->dt->addColumn('number');
        $this->dt->addColumn('number');

        $rows = array(
            array(Carbon::parse('March 24th, 1988'), 12345, 67890),
            234.234
        );

        $this->dt->addRows($rows);

        $cols = $this->dt->getColumns();
        $rows = $this->dt->getRows();

        $this->assertEquals($cols[0]['type'], 'date');
        $this->assertEquals($cols[1]['type'], 'number');
        $this->assertEquals($rows[0]['c'][0]['v'], 'Date(1988, 2, 24, 0, 0, 0)');
        $this->assertEquals($rows[0]['c'][1]['v'], 12345);
        $this->assertEquals($rows[0]['c'][2]['v'], 67890);
        $this->assertEquals($rows[1]['c'][0]['v'], 'Date(1988, 2, 25, 0, 0, 0)');
        $this->assertEquals($rows[1]['c'][1]['v'], 1122);
        $this->assertEquals($rows[1]['c'][2]['v'], 3344);
    }

    /**
     * @depends testAddColumnWithTypeAndDescription
     * @expectedException Lavacharts\Exceptions\InvalidCellCount
     */
    public function testAddMoreCellsThanColumns()
    {
        $this->dt->addColumn('date');
        $this->dt->addColumn('number');
        $this->dt->addRow(array(Carbon::parse('March 24th, 1988'), 12345, 67890));
    }

    /*
     * @depends testAddColumnWithTypeAndDescription
     *
    public function testAddDateColumnWithShortSyntax()
    {
        $this->dt->addDateCol();
        $this->dt->addColumn('number');
        $this->dt->addRow(array(Carbon::parse('March 24th, 1988'), 12345, 67890));
    }*/
}

//dataProvider
//expectedException
