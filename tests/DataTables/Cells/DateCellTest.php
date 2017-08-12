<?php

namespace Khill\Lavacharts\Tests\DataTables\Cells;

use Carbon\Carbon;
use Khill\Lavacharts\Support\Options;
use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\DataTables\Cells\DateCell;
use Mockery;

class DateCellTest extends ProvidersTestCase
{
    /**
     * @covers \Khill\Lavacharts\DataTables\Cells\DateCell::__construct()
     */
    public function testConstructorArgs()
    {
        /** @var Carbon $mockCarbon */
        $mockCarbon = Mockery::mock('\Carbon\Carbon[parse]', ['2015-09-04 9:31:00']);

        $cell = new DateCell($mockCarbon, 'start', ['color' => 'red']);

        $this->assertInstanceOf(Carbon::class, $cell->getValue());
        $this->assertEquals('start', $cell->getFormat());
        $this->assertInstanceOf(Options::class, $cell->getOptions());
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Cells\DateCell::create()
     * @covers \Khill\Lavacharts\DataTables\Cells\DateCell::__toString()
     */
    public function testCreatingDateCellStatically()
    {
        $cell = DateCell::create('3/24/1988 8:01:05');
        $this->assertEquals('Date(1988,2,24,8,1,5)', $cell);

        $cell = DateCell::create('March 24th, 1988 8:01:05');
        $this->assertEquals('Date(1988,2,24,8,1,5)', $cell);
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Cells\DateCell::create()
     * @covers \Khill\Lavacharts\DataTables\Cells\DateCell::__toString()
     */
    public function testCreateFromFormat()
    {
        $cell = DateCell::createFromFormat('g:ia \o\n l jS F Y', '5:45pm on Saturday 24th March 2012');

        $this->assertEquals('Date(2012,2,24,17,45,0)', $cell);
    }

    /**
     * @expectedException \Exception
     * @covers \Khill\Lavacharts\DataTables\Cells\DateCell::create()
     */
    public function testParseStringWithBadDateTimeString()
    {
        DateCell::create('132/06/199210');
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\UndefinedDateCellException
     * @covers \Khill\Lavacharts\DataTables\Cells\DateCell::create()
     */
    public function testCreatingDateCellStaticallyWithBadType()
    {
        $a = DateCell::create('This is not a datetime');
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\UndefinedDateCellException
     * @covers \Khill\Lavacharts\DataTables\Cells\DateCell::create()
     */
    public function testCreatingDateCellStaticallyWithCreateFromFormatAndBadFormatString()
    {
        DateCell::createFromFormat('1/2/2003', 'sushi');
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\UndefinedDateCellException
     * @covers \Khill\Lavacharts\DataTables\Cells\DateCell::create()
     */
    public function testCreatingDateCellStaticallyWithCreateFromFormatAndBadFormatType()
    {
        DateCell::createFromFormat('1/2/2003', ['imnotaformat']);
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Cells\DateCell::toArray()
     */
    public function testDateCellToArray()
    {
        $mockCarbon = Mockery::mock('\Carbon\Carbon[parse]', ['2015-09-04 9:31:00']);

        $cell = new DateCell($mockCarbon);

        $cellArr = $cell->toArray();

        $this->assertTrue(is_array($cellArr));

        $this->assertArrayHasKey('v', $cellArr);
        $this->assertEquals('Date(2015,8,4,9,31,0)', $cellArr['v']);
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Cells\DateCell::jsonSerialize()
     */
    public function testDateCellJsonSerialization()
    {
        $mockCarbon = Mockery::mock('\Carbon\Carbon[parse]', ['2015-09-04 9:31:00']);

        $cell = new DateCell($mockCarbon);

        $this->assertEquals('{"v":"Date(2015,8,4,9,31,0)"}', json_encode($cell));
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Cells\DateCell::toJson()
     */
    public function testDateCellToJson()
    {
        $mockCarbon = Mockery::mock('\Carbon\Carbon[parse]', ['2015-09-04 9:31:00']);

        $cell = new DateCell($mockCarbon);

        $this->assertEquals('{"v":"Date(2015,8,4,9,31,0)"}', $cell->toJson());
    }
}
