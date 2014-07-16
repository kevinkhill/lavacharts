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
}

//dataProvider
//expectedException
