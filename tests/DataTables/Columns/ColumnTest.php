<?php

namespace Khill\Lavacharts\Tests\DataTables\Columns;

use \Mockery as m;
use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\DataTables\Columns\Column;

class ColumnTest extends ProvidersTestCase
{
    public $MockColumn;

    public function setUp()
    {
        parent::setUp();

        $this->MockColumn = new Column('number', 'MyLabel', 'new_col_1');
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Columns\Column::__construct
     */
    public function testConstructorForLabelAndId()
    {
        $this->assertEquals('number', $this->getPrivateProperty($this->MockColumn, 'type'));
        $this->assertEquals('MyLabel', $this->getPrivateProperty($this->MockColumn, 'label'));
        $this->assertEquals('new_col_1', $this->getPrivateProperty($this->MockColumn, 'id'));
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Columns\Column::getType
     */
    public function testGetType()
    {
        $this->assertEquals('number', $this->MockColumn->getType());
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Columns\Column::getLabel
     */
    public function testGetLabel()
    {
        $this->assertEquals('MyLabel', $this->MockColumn->getLabel());
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Columns\Column::getId
     */
    public function testGetId()
    {
        $this->assertEquals('new_col_1', $this->MockColumn->getId());
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Columns\Column::setFormat
     */
    public function testSetFormat()
    {
        $mockFormat = m::mock('\Khill\Lavacharts\DataTables\Formats\NumberFormat');

        $this->MockColumn->setFormat($mockFormat);

        $format = $this->getPrivateProperty($this->MockColumn, 'format');

        $this->assertInstanceOf('\Khill\Lavacharts\DataTables\Formats\Format', $format);
    }

    /**
     * @depends testSetFormat
     * @covers \Khill\Lavacharts\DataTables\Columns\Column::isFormatted
     */
    public function testIsFormatted()
    {
        $mockFormat = m::mock('\Khill\Lavacharts\DataTables\Formats\NumberFormat');

        $this->MockColumn->setFormat($mockFormat);

        $this->assertTrue($this->MockColumn->isFormatted());
    }

    /**
     * @depends testSetFormat
     * @covers \Khill\Lavacharts\DataTables\Columns\Column::getFormat
     */
    public function testGetFormat()
    {
        $mockFormat = m::mock('\Khill\Lavacharts\DataTables\Formats\NumberFormat');

        $this->MockColumn->setFormat($mockFormat);

        $this->assertInstanceOf('\Khill\Lavacharts\DataTables\Formats\Format', $this->MockColumn->getFormat());
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Columns\Column::setRole
     */
    public function testSetRole()
    {
        $mockRole = m::mock('\Khill\Lavacharts\DataTables\Columns\ColumnRole', ['interval']);

        $this->MockColumn->setRole($mockRole);

        $role = $this->getPrivateProperty($this->MockColumn, 'role');

        $this->assertInstanceOf('\Khill\Lavacharts\DataTables\Columns\ColumnRole', $role);
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Columns\Column::getRole
     */
    public function testGetRole()
    {
        $mockRole = m::mock('\Khill\Lavacharts\DataTables\Columns\ColumnRole', ['interval']);

        $this->MockColumn->setRole($mockRole);

        $this->assertInstanceOf('\Khill\Lavacharts\DataTables\Columns\ColumnRole', $this->MockColumn->getRole());
    }

    /**
     * @depends testSetRole
     * @covers \Khill\Lavacharts\DataTables\Columns\Column::jsonSerialize
     */
    public function testJsonSerialization()
    {
        $mockRole = m::mock('\Khill\Lavacharts\DataTables\Columns\ColumnRole', ['interval'])
                    ->shouldReceive('jsonSerialize')
                    ->once()
                    ->andReturn(['role'=>'interval'])
                    ->getMock();

        $this->MockColumn->setRole($mockRole);

        $json = '{"type":"number","label":"MyLabel","id":"new_col_1","p":{"role":"interval"}}';

        $this->assertEquals($json, json_encode($this->MockColumn));
    }
}



