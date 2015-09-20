<?php

namespace Khill\Lavacharts\Tests\DataTables\Cells;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\DataTables\Cells\DateCell;

class DateCellTest extends ProvidersTestCase
{
    public $Cell;

    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Cells\DateCell::__construct
     */
    public function testConstructorArgs()
    {
        $mockCarbon = \Mockery::mock('\Carbon\Carbon[parse]', ['2015-09-04 9:31:00']);

        $column = new DateCell($mockCarbon, 'start', ['color'=>'red']);

        $this->assertInstanceOf('Carbon\Carbon', $this->getPrivateProperty($column, 'v'));
        $this->assertEquals('start', $this->getPrivateProperty($column, 'f'));
        $this->assertTrue(is_array($this->getPrivateProperty($column, 'p')));
    }


    /**
     * @covers \Khill\Lavacharts\DataTables\Cells\DateCell::parseString
     * @covers \Khill\Lavacharts\DataTables\Cells\DateCell::__toString
     */
    public function testParseStringWithNoFormat()
    {
        $cell = DateCell::parseString('3/24/1988 8:01:05');
        $this->assertEquals('Date(1988,2,24,8,1,5)', (string) $cell);

        $cell = DateCell::parseString('March 24th, 1988 8:01:05');
        $this->assertEquals('Date(1988,2,24,8,1,5)', (string) $cell);
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Cells\DateCell::parseString
     * @covers \Khill\Lavacharts\DataTables\Cells\DateCell::__toString
     */
    public function testParseStringWithFormat()
    {

        $cell = DateCell::parseString('5:45pm on Saturday 24th March 2012', 'g:ia \o\n l jS F Y');

        $this->assertEquals('Date(2012,2,24,17,45,0)', (string) $cell);
    }

    /**
     * @expectedException \Exception
     * @covers \Khill\Lavacharts\DataTables\Cells\DateCell::parseString
     */
    public function testParseStringWithBadDateTimeString()
    {
        DateCell::parseString('132/06/199210');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidDateTimeString
     * @covers \Khill\Lavacharts\DataTables\Cells\DateCell::parseString
     */
    public function testParseStringWithBadTypesForDateTime($badTypes)
    {
        DateCell::parseString($badTypes);
    }

    /**
     * @expectedException \Exception
     * @covers \Khill\Lavacharts\DataTables\Cells\DateCell::parseString
     */
    public function testParseStringWithBadFormatString()
    {
        DateCell::parseString('1/2/2003', 'sushi');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidDateTimeFormat
     * @covers \Khill\Lavacharts\DataTables\Cells\DateCell::parseString
     */
    public function testParseStringWithBadTypesForFormat($badTypes)
    {
        DateCell::parseString('1/2/2003', $badTypes);
    }

    /**
     * @depends testConstructorArgs
     * @covers \Khill\Lavacharts\DataTables\Cells\DateCell::jsonSerialize
     */
    public function testJsonSerialization()
    {
        $mockCarbon = \Mockery::mock('\Carbon\Carbon[parse]', ['2015-09-04 9:31:00']);

        $cell = new DateCell($mockCarbon);

        $this->assertEquals('"Date(2015,8,4,9,31,0)"', json_encode($cell));
    }
}
