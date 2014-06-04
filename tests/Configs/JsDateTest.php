<?php namespace Khill\Lavacharts\Tests\Configs;

use Khill\Lavacharts\Tests\TestCase;
use Khill\Lavacharts\Configs\jsDate;

class JsDateTest extends TestCase {
/*
    public function setUp()
    {
        parent::setUp();
    }
*/
    public function testIfInstanceOfJsDate()
    {
        $this->assertInstanceOf('Khill\Lavacharts\Configs\jsDate', new jsDate());
    }

    public function testConstructorNoAssignmentsIsNulled()
    {
        $date = new jsDate();

        $this->assertEquals(null, $date->year);
        $this->assertEquals(null, $date->month);
        $this->assertEquals(null, $date->day);
        $this->assertEquals(null, $date->hour);
        $this->assertEquals(null, $date->minute);
        $this->assertEquals(null, $date->second);
        $this->assertEquals(null, $date->millisecond);
    }

    public function testConstructorWithAssignments()
    {
        $date = new jsDate(2014, 1, 2, 3, 4, 5, 6);

        $this->assertEquals(2014, $date->year);
        $this->assertEquals(1,    $date->month);
        $this->assertEquals(2,    $date->day);
        $this->assertEquals(3,    $date->hour);
        $this->assertEquals(4,    $date->minute);
        $this->assertEquals(5,    $date->second);
        $this->assertEquals(6,    $date->millisecond);
    }

    public function testParseWithArray()
    {
        $dateArr = array(2014, 1, 2, 3, 4, 5, 6);
        $date = new jsDate();
        $date->parse($dateArr);

        $this->assertEquals(2014, $date->year);
        $this->assertEquals(1,    $date->month);
        $this->assertEquals(2,    $date->day);
        $this->assertEquals(3,    $date->hour);
        $this->assertEquals(4,    $date->minute);
        $this->assertEquals(5,    $date->second);
        $this->assertEquals(6,    $date->millisecond);
    }

    public function testParseWithString()
    {
        $dateStr = '2014-12-25 13:04:05.6';
        $date = new jsDate();
        $date->parse($dateStr);

        $this->assertEquals(2014, $date->year);
        $this->assertEquals(12,   $date->month);
        $this->assertEquals(25,   $date->day);
        $this->assertEquals(13,   $date->hour);
        $this->assertEquals(4,    $date->minute);
        $this->assertEquals(5,    $date->second);
        $this->assertEquals(600,  $date->millisecond);
    }

    public function testFullyDefinedJsOutput()
    {
        $this->expectOutputString('Date(2014, 1, 2, 3, 4, 5, 6)');

        $date = new jsDate(2014, 1, 2, 3, 4, 5, 6);

        echo $date->toString();
    }

    public function testNoMillisecondsJsOutput()
    {
        $this->expectOutputString('Date(2014, 1, 2, 3, 4, 5)');

        $date = new jsDate(2014, 1, 2, 3, 4, 5);

        echo $date->toString();
    }

    public function testNoMillisecondsNoSecondsJsOutput()
    {
        $this->expectOutputString('Date(2014, 1, 2, 3, 4)');

        $date = new jsDate(2014, 1, 2, 3, 4);

        echo $date->toString();
    }

    public function testNoMillisecondsNoSecondsNoMinutesJsOutput()
    {
        $this->expectOutputString('Date(2014, 1, 2, 3)');

        $date = new jsDate(2014, 1, 2, 3);

        echo $date->toString();
    }

    public function testNoMillisecondsNoSecondsNoMinutesNoHoursJsOutput()
    {
        $this->expectOutputString('Date(2014, 1, 2)');

        $date = new jsDate(2014, 1, 2);

        echo $date->toString();
    }

    public function testNoMillisecondsNoSecondsNoMinutesNoHoursNoDayJsOutput()
    {
        $this->expectOutputString('Date(2014, 1, 0)');

        $date = new jsDate(2014, 1);

        echo $date->toString();
    }

    public function testNoMillisecondsNoSecondsNoMinutesNoHoursNoDayNoMonthJsOutput()
    {
        $this->expectOutputString('Date(2014, 0, 0)');

        $date = new jsDate(2014);

        echo $date->toString();
    }

    public function testEmptyObjectJsOutput()
    {
        $this->expectOutputString('Date(0, 0, 0)');

        $date = new jsDate();

        echo $date->toString();
    }

}
