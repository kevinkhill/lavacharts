<?php

namespace Khill\Lavacharts\Tests\DataTables\Columns;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\DataTables\Columns\ColumnFactory;

class ColumnFactoryTest extends ProvidersTestCase
{
    /**
     * @dataProvider columnTypeProvider
     * @covers \Khill\Lavacharts\DataTables\Columns\ColumnFactory::create
     */
    public function testCreateColumnsWithType($columnType)
    {
        $column = ColumnFactory::create($columnType);

        $this->assertInstanceOf('\Khill\Lavacharts\DataTables\Columns\Column', $column);
        $this->assertEquals($columnType, $this->getPrivateProperty($column, 'type'));
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidColumnType
     * @covers \Khill\Lavacharts\DataTables\Columns\ColumnFactory::create
     */
    public function testCreateColumnsWithBadValue()
    {
        ColumnFactory::create('milkshakes');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidColumnType
     * @covers \Khill\Lavacharts\DataTables\Columns\ColumnFactory::create
     */
    public function testCreateColumnsWithBadTypes($badTypes)
    {
        ColumnFactory::create($badTypes);
    }

    /**
     * @dataProvider columnTypeProvider
     * @depends testCreateColumnsWithType
     * @covers \Khill\Lavacharts\DataTables\Columns\ColumnFactory::create
     */
    public function testCreateColumnsWithTypeAndLabel($columnType)
    {
        $column = ColumnFactory::create($columnType, 'Label');

        $this->assertInstanceOf('\Khill\Lavacharts\DataTables\Columns\Column', $column);
        $this->assertEquals($columnType, $this->getPrivateProperty($column, 'type'));
        $this->assertEquals('Label', $this->getPrivateProperty($column, 'label'));
    }

    /**
     * @dataProvider columnTypeProvider
     * @depends testCreateColumnsWithTypeAndLabel
     * @covers \Khill\Lavacharts\DataTables\Columns\ColumnFactory::create
     */
    public function testCreateColumnsWithTypeAndLabelAndFormat($columnType)
    {
        $mockFormat = \Mockery::mock('\Khill\Lavacharts\DataTables\Formats\NumberFormat')->makePartial();

        $column = ColumnFactory::create($columnType, 'Label', $mockFormat);

        $this->assertInstanceOf('\Khill\Lavacharts\DataTables\Columns\Column', $column);
        $this->assertEquals($columnType, $this->getPrivateProperty($column, 'type'));
        $this->assertEquals('Label', $this->getPrivateProperty($column, 'label'));
        $this->assertInstanceOf('\Khill\Lavacharts\DataTables\Formats\NumberFormat', $this->getPrivateProperty($column, 'format'));
    }

    /**
     * @dataProvider columnTypeProvider
     * @depends testCreateColumnsWithTypeAndLabelAndFormat
     * @covers \Khill\Lavacharts\DataTables\Columns\ColumnFactory::create
     */
    public function testCreateColumnsWithTypeAndLabelAndFormatAndRole($columnType)
    {
        $mockFormat = \Mockery::mock('\Khill\Lavacharts\DataTables\Formats\NumberFormat')->makePartial();

        $column = ColumnFactory::create($columnType, 'Label', $mockFormat, 'interval');

        $this->assertInstanceOf('\Khill\Lavacharts\DataTables\Columns\Column', $column);
        $this->assertEquals($columnType, $this->getPrivateProperty($column, 'type'));
        $this->assertEquals('Label', $this->getPrivateProperty($column, 'label'));
        $this->assertInstanceOf('\Khill\Lavacharts\DataTables\Formats\NumberFormat', $this->getPrivateProperty($column, 'format'));
        $this->assertEquals('interval', $this->getPrivateProperty($column, 'role'));
    }

    /**
     * @depends testCreateColumnsWithTypeAndLabelAndFormatAndRole
     * @covers \Khill\Lavacharts\DataTables\Columns\ColumnFactory::create
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidColumnRole
     */
    public function testCreateColumnsWithTypeAndLabelAndFormatAndRoleWithBadRole()
    {
        $mockFormat = \Mockery::mock('\Khill\Lavacharts\DataTables\Formats\NumberFormat')->makePartial();

        ColumnFactory::create('number', 'Label', $mockFormat, 'tacos');
    }
}
