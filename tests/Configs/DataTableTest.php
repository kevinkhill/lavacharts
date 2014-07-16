<?php namespace Khill\Lavacharts\Tests\Configs;

use Khill\Lavacharts\Lavacharts;
use Khill\Lavacharts\Configs\DataTable;

class DataTableTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testIfInstanceOfDataTable()
    {
        $lc = new Lavacharts;

        $this->assertInstanceOf('Khill\Lavacharts\Configs\DataTable', $lc->DataTable());
    }

    public function testAddColumnWithTypeOnly()
    {
        $lc = new Lavacharts;
        $dt = $lc->DataTable();

        $dt->addColumn('date');

        $cols = $dt->getColumns();

        $this->assertEquals($cols[0]['type'], 'date');
    }

    public function testAddColumnWithTypeAndDescription()
    {
        $lc = new Lavacharts;
        $dt = $lc->DataTable();

        $dt->addColumn('date', 'Days in March');

        $cols = $dt->getColumns();

        $this->assertEquals($cols[0]['type'],  'date');
        $this->assertEquals($cols[0]['label'], 'Days in March');
    }

    public function testAddColumnWithTypeAndDescriptionAndId()
    {
        $lc = new Lavacharts;
        $dt = $lc->DataTable();

        $dt->addColumn('date', 'Days in March', 'march-dates');

        $cols = $dt->getColumns();

        $this->assertEquals($cols[0]['type'],  'date');
        $this->assertEquals($cols[0]['label'], 'Days in March');
        $this->assertEquals($cols[0]['id'],    'march-dates');
    }

    public function testAddColumnWithArrayOfTypeAndDescriptionAndId()
    {
        $lc = new Lavacharts;
        $dt = $lc->DataTable();

        $dt->addColumn(array('date', 'Days in March', 'march-dates'));

        $cols = $dt->getColumns();

        $this->assertEquals($cols[0]['type'],  'date');
        $this->assertEquals($cols[0]['label'], 'Days in March');
        $this->assertEquals($cols[0]['id'],    'march-dates');
    }

    /**
     * @depends testAddColumnWithArrayOfTypeAndDescriptionAndId
     */
    /*public function testAddMultipleColumnsWithArrayOfTypeAndDescriptionAndId()
    {
        $lc = new Lavacharts;
        $dt = $lc->DataTable();

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
    }*/

}

//dataProvider
//expectedException
