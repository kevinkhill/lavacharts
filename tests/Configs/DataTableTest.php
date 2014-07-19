<?php namespace Khill\Lavacharts\Tests\Configs;

use Khill\Lavacharts\Lavacharts;
use Khill\Lavacharts\Configs\DataTable;
use Carbon\Carbon;

class DataTableTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->lc = new Lavacharts;
    }

    public function testIfInstanceOfDataTable()
    {
        $this->assertInstanceOf('Khill\Lavacharts\Configs\DataTable', $this->lc->DataTable());
    }

    public function testAddColumnWithTypeOnly()
    {
        $dt = $this->lc->DataTable();

        $dt->addColumn('date');

        $cols = $dt->getColumns();

        $this->assertEquals($cols[0]['type'], 'date');
    }

    public function testAddColumnWithArrayOfTypeOnly()
    {
        $dt = $this->lc->DataTable();

        $dt->addColumn(array('date'));

        $cols = $dt->getColumns();

        $this->assertEquals($cols[0]['type'], 'date');
    }

    public function testAddColumnWithTypeAndDescription()
    {
        $dt = $this->lc->DataTable();

        $dt->addColumn('date', 'Days in March');

        $cols = $dt->getColumns();

        $this->assertEquals($cols[0]['type'],  'date');
        $this->assertEquals($cols[0]['label'], 'Days in March');
    }

    public function testAddColumnWithArrafOfTypeAndDescription()
    {
        $dt = $this->lc->DataTable();

        $dt->addColumn(array('date', 'Days in March'));

        $cols = $dt->getColumns();

        $this->assertEquals($cols[0]['type'],  'date');
        $this->assertEquals($cols[0]['label'], 'Days in March');
    }

    public function testAddColumnWithTypeAndDescriptionAndId()
    {
        $dt = $this->lc->DataTable();

        $dt->addColumn('date', 'Days in March', 'march-dates');

        $cols = $dt->getColumns();

        $this->assertEquals($cols[0]['type'],  'date');
        $this->assertEquals($cols[0]['label'], 'Days in March');
        $this->assertEquals($cols[0]['id'],    'march-dates');
    }

    public function testAddColumnWithArrayOfTypeAndDescriptionAndId()
    {
        $dt = $this->lc->DataTable();

        $dt->addColumn(array('date', 'Days in March', 'march-dates'));

        $cols = $dt->getColumns();

        $this->assertEquals($cols[0]['type'],  'date');
        $this->assertEquals($cols[0]['label'], 'Days in March');
        $this->assertEquals($cols[0]['id'],    'march-dates');
    }

    /**
     * @depends testAddColumnWithArrayOfTypeAndDescriptionAndId
     */
    public function testAddMultipleColumnsWithArrayOfTypeAndDescriptionAndId()
    {
        $dt = $this->lc->DataTable();

        $dt->addColumns(array(
            array('date', 'Days in March', 'march-dates'),
            array('number', 'Day of the Week', 'dotw'),
            array('number', 'Temperature', 'temp'),
        ));

        $cols = $dt->getColumns();

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
     * @depends testAddColumnWithTypeOnly
     */
    public function testAddRowWithTypeDateOnly()
    {
        $dt = $this->lc->DataTable();

        $dt->addColumn('date');

        $dt->addRow(array(Carbon::parse('March 24th, 1988')));

        $cols = $dt->getColumns();
        $rows = $dt->getRows();

        $this->assertEquals($cols[0]['type'], 'date');
        $this->assertEquals($rows[0]['c'][0]['v'], 'Date(1988, 3, 24, 0, 0, 0)');
    }

    /**
     * @depends testAddColumnWithTypeAndDescription
     */
    public function testAddRowWithMultipleColumnsWithDateAndNumbers()
    {
        $dt = $this->lc->DataTable();

        $dt->addColumn('date');
        $dt->addColumn('number');
        $dt->addColumn('number');

        $dt->addRow(array(Carbon::parse('March 24th, 1988'), 12345, 67890));

        $cols = $dt->getColumns();
        $rows = $dt->getRows();
        
        $this->assertEquals($cols[0]['type'], 'date');
        $this->assertEquals($cols[1]['type'], 'number');
        $this->assertEquals($rows[0]['c'][0]['v'], 'Date(1988, 3, 24, 0, 0, 0)');
        $this->assertEquals($rows[0]['c'][1]['v'], 12345);
        $this->assertEquals($rows[0]['c'][2]['v'], 67890);
    }

    /**
     * @depends testAddColumnWithTypeAndDescription
     */
    public function testAddMultipleRowsWithMultipleColumnsWithDateAndNumbers()
    {
        $dt = $this->lc->DataTable();

        $dt->addColumn('date');
        $dt->addColumn('number');
        $dt->addColumn('number');

        $rows = array(
            array(Carbon::parse('March 24th, 1988'), 12345, 67890),
            array(Carbon::parse('March 25th, 1988'), 1122, 3344)
        );

        $dt->addRows($rows);

        $cols = $dt->getColumns();
        $rows = $dt->getRows();
        
        $this->assertEquals($cols[0]['type'], 'date');
        $this->assertEquals($cols[1]['type'], 'number');
        $this->assertEquals($rows[0]['c'][0]['v'], 'Date(1988, 3, 24, 0, 0, 0)');
        $this->assertEquals($rows[0]['c'][1]['v'], 12345);
        $this->assertEquals($rows[0]['c'][2]['v'], 67890);
        $this->assertEquals($rows[1]['c'][0]['v'], 'Date(1988, 3, 25, 0, 0, 0)');
        $this->assertEquals($rows[1]['c'][1]['v'], 1122);
        $this->assertEquals($rows[1]['c'][2]['v'], 3344);
    }

    /**
     * @depends testAddColumnWithTypeAndDescription
     * @expectedException Khill\Lavacharts\Exceptions\InvalidRowDefinition
     */
    public function testAddBadMultipleRowsWithMultipleColumnsWithDateAndNumbers()
    {
        $dt = $this->lc->DataTable();

        $dt->addColumn('date');
        $dt->addColumn('number');
        $dt->addColumn('number');

        $rows = array(
            array(Carbon::parse('March 24th, 1988'), 12345, 67890),
            234.234
        );

        $dt->addRows($rows);

        $cols = $dt->getColumns();
        $rows = $dt->getRows();
        
        $this->assertEquals($cols[0]['type'], 'date');
        $this->assertEquals($cols[1]['type'], 'number');
        $this->assertEquals($rows[0]['c'][0]['v'], 'Date(1988, 3, 24, 0, 0, 0)');
        $this->assertEquals($rows[0]['c'][1]['v'], 12345);
        $this->assertEquals($rows[0]['c'][2]['v'], 67890);
        $this->assertEquals($rows[1]['c'][0]['v'], 'Date(1988, 3, 25, 0, 0, 0)');
        $this->assertEquals($rows[1]['c'][1]['v'], 1122);
        $this->assertEquals($rows[1]['c'][2]['v'], 3344);
    }

    /**
     * @depends testAddColumnWithTypeAndDescription
     * @expectedException Khill\Lavacharts\Exceptions\InvalidCellCount
     */
    public function testAddMoreCellsThanColumns()
    {
        $dt = $this->lc->DataTable();

        $dt->addColumn('date');
        $dt->addColumn('number');
        $dt->addRow(array(Carbon::parse('March 24th, 1988'), 12345, 67890));
    }
}

//dataProvider
//expectedException
