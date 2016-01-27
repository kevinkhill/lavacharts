<?php

namespace Khill\Lavacharts\Tests\DataTables\Columns;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\DataTables\Columns\Column;

class ColumnTest extends ProvidersTestCase
{
    public $Column;

    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Columns\Column::__construct
     */
    public function testConstructorWithType()
    {
        $column = new Column('number');

        $this->assertEquals('number', $this->getPrivateProperty($column, 'type'));
    }

    /**
     * @depends testConstructorWithType
     * @covers \Khill\Lavacharts\DataTables\Columns\Column::__construct
     */
    public function testConstructorWithTypeAndLabel()
    {
        $column = new Column('number', 'MyLabel');

        $this->assertEquals('MyLabel', $this->getPrivateProperty($column, 'label'));
    }

    /**
     * @depends testConstructorWithTypeAndLabel
     * @covers \Khill\Lavacharts\DataTables\Columns\Column::__construct
     */
    public function testConstructorWithTypeAndLabelAndFormat()
    {
        $mockFormat = \Mockery::mock('\Khill\Lavacharts\DataTables\Formats\NumberFormat')->makePartial();
        $column = new Column('number', 'MyLabel', $mockFormat);

        $this->assertInstanceOf('\Khill\Lavacharts\DataTables\Formats\NumberFormat', $this->getPrivateProperty($column, 'format'));
    }

    /**
     * @depends testConstructorWithTypeAndLabelAndFormat
     * @covers \Khill\Lavacharts\DataTables\Columns\Column::__construct
     */
    public function testConstructorWithTypeAndLabelAndFormatAndRole()
    {
        $mockFormat = \Mockery::mock('\Khill\Lavacharts\DataTables\Formats\NumberFormat')->makePartial();
        $column = new Column('number', 'MyLabel', $mockFormat, 'interval');

        $this->assertEquals('interval', $this->getPrivateProperty($column, 'role'));
    }

    /**
     * @depends testConstructorWithType
     * @covers \Khill\Lavacharts\DataTables\Columns\Column::getType
     */
    public function testGetType()
    {
        $column = new Column('number');

        $this->assertEquals('number', $column->getType());
    }

    /**
     * @depends testConstructorWithTypeAndLabel
     * @covers \Khill\Lavacharts\DataTables\Columns\Column::getLabel
     */
    public function testGetLabel()
    {
        $column = new Column('number', 'MyLabel');

        $this->assertEquals('MyLabel', $column->getLabel());
    }

    /**
     * @depends testConstructorWithTypeAndLabelAndFormat
     * @covers \Khill\Lavacharts\DataTables\Columns\Column::isFormatted
     */
    public function testIsFormatted()
    {
        $mockFormat = \Mockery::mock('\Khill\Lavacharts\DataTables\Formats\NumberFormat')->makePartial();

        $column = new Column('number', 'MyLabel', $mockFormat);

        $this->assertTrue($column->isFormatted());
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Columns\Column::getFormat
     */
    public function testGetFormat()
    {
        $mockFormat = \Mockery::mock('\Khill\Lavacharts\DataTables\Formats\NumberFormat')->makePartial();

        $column = new Column('number', 'MyLabel', $mockFormat);

        $this->assertInstanceOf('\Khill\Lavacharts\DataTables\Formats\NumberFormat', $column->getFormat());
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Columns\Column::getRole
     */
    public function testGetRole()
    {
        $mockFormat = \Mockery::mock('\Khill\Lavacharts\DataTables\Formats\NumberFormat')->makePartial();

        $column = new Column('number', 'MyLabel', $mockFormat, 'interval');

        $this->assertEquals('interval', $column->getRole());
    }

    /**
     * @depends testConstructorWithTypeAndLabelAndFormatAndRole
     * @covers \Khill\Lavacharts\DataTables\Columns\Column::jsonSerialize
     */
    public function testJsonSerialization()
    {
        $mockFormat = \Mockery::mock('\Khill\Lavacharts\DataTables\Formats\NumberFormat')->makePartial();

        $column = new Column('number', 'MyLabel', $mockFormat, 'interval');

        $json = '{"type":"number","label":"MyLabel","p":{"role":"interval"}}';

        $this->assertEquals($json, json_encode($column));
    }
}



