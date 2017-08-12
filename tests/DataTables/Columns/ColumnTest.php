<?php

namespace Khill\Lavacharts\Tests\DataTables\Columns;

use Khill\Lavacharts\DataTables\Columns\Format;
use Khill\Lavacharts\DataTables\Columns\Role;
use Khill\Lavacharts\Support\Options;
use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\DataTables\Columns\Column;
use Mockery;

/**
 * @property Role   mockRole
 * @property Format mockFormat
 * @property string columnJson
 */
class ColumnTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->columnJson = '{"type":"number","label":"MyLabel","p":{"thisIs":"anOption","role":"interval"}}';

        $this->mockFormat = Mockery::mock(Format::class);

        $this->mockRole = Mockery::mock(Role::class);
        $this->mockRole->shouldReceive(['__toString' => 'interval']);
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Columns\Column::__construct()
     * @covers \Khill\Lavacharts\DataTables\Columns\Column::getType()
     * @covers \Khill\Lavacharts\DataTables\Columns\Column::getLabel()
     * @covers \Khill\Lavacharts\DataTables\Columns\Column::getFormat()
     * @covers \Khill\Lavacharts\DataTables\Columns\Column::getRole()
     * @covers \Khill\Lavacharts\DataTables\Columns\Column::getOptions()
     */
    public function testConstructorWithTypeAndDefaultArgs()
    {
        $column = new Column('number');

        $this->assertEquals('number', $column->getType());
        $this->assertEquals('', $column->getLabel());
        $this->assertNull($column->getFormat());
        $this->assertEquals('', $column->getRole());
        $this->assertInstanceOf(Options::class, $column->getOptions());
        $this->assertCount(0, $column->getOptions());
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Columns\Column::__construct
     */
    public function testConstructorWithTypeAndLabel()
    {
        $column = new Column('number', 'MyLabel');

        $this->assertEquals('MyLabel', $column->getLabel());
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Columns\Column::isFormatted()
     */
    public function testConstructorWithTypeAndLabelAndFormat()
    {
        $column = new Column('number', 'MyLabel', $this->mockFormat);

        $this->assertTrue($column->isFormatted());
        $this->assertInstanceOf(Format::class, $column->getFormat());
    }

    public function testConstructorWithTypeAndLabelAndFormatAndRole()
    {
        $column = new Column('number', 'MyLabel', $this->mockFormat, $this->mockRole);

        $this->assertInstanceOf(Role::class, $column->getRole());
        $this->assertEquals('interval', $column->getRole());
    }

    public function testConstructorWithTypeAndLabelAndFormatAndRoleAndOptions()
    {
        $column = new Column('number', 'MyLabel', $this->mockFormat, $this->mockRole, ['thisIs' => 'anOption']);

        $this->assertInstanceOf(Options::class, $column->getOptions());
        $this->assertEquals('anOption', $column->getOption('thisIs'));
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Columns\Column::toArray()
     */
    public function testColumnToArray()
    {
        $column = new Column('number', 'MyLabel', $this->mockFormat, $this->mockRole, ['thisIs' => 'anOption']);

        $columnArr = $column->toArray();

        $this->assertTrue(is_array($columnArr));

        $this->assertArrayHasKey('type', $columnArr);
        $this->assertEquals('number', $columnArr['type']);

        $this->assertArrayHasKey('label', $columnArr);
        $this->assertEquals('MyLabel', $columnArr['label']);

//        Not part of the JSON schema
//        $this->assertArrayHasKey('format', $columnArr);
//        $this->assertTrue(is_array($columnArr['format']));

        $this->assertArrayHasKey('p', $columnArr);
        $this->assertTrue(is_array($columnArr['p']));

        $this->assertArrayHasKey('role', $columnArr['p']);
        $this->assertTrue(is_string($columnArr['p']['role']));

        $this->assertArrayHasKey('thisIs', $columnArr['p']);
        $this->assertEquals('anOption', $columnArr['p']['thisIs']);
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Columns\Column::jsonSerialize()
     */
    public function testColumnJsonSerialization()
    {
        $column = new Column('number', 'MyLabel', $this->mockFormat, $this->mockRole, ['thisIs' => 'anOption']);

        $this->assertEquals(
            $this->columnJson,
            json_encode($column)
        );
    }

    /**
     * @depends testColumnJsonSerialization
     * @covers \Khill\Lavacharts\DataTables\Columns\Column::toJson()
     */
    public function testColumnToJson()
    {
        $column = new Column('number', 'MyLabel', $this->mockFormat, $this->mockRole, ['thisIs' => 'anOption']);

        $this->assertEquals(
            $this->columnJson,
            $column->toJson()
        );
    }
}



