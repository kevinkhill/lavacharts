<?php namespace Khill\Lavacharts\Tests;

//http://phpunit.de/manual/3.8/en/writing-tests-for-phpunit.html

use Khill\Lavacharts\Configs\jsDate;

class JsDateTest extends \Orchestra\Testbench\TestCase {
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

    public function testConstructorWithAssignments()
    {
        $date = new jsDate(2014, 1, 2, 3, 4, 5, 6);

        $this->assertEquals($date->year, 2014);
        $this->assertEquals($date->month, 1);
        $this->assertEquals($date->day, 2);
        $this->assertEquals($date->hour, 3);
        $this->assertEquals($date->minute, 4);
        $this->assertEquals($date->second, 5);
        $this->assertEquals($date->millisecond, 6);
    }

    public function testConstructorWithString()
    {
        $dateStr = '2014-12-25 13:04:05.6';
        $date = new jsDate($dateStr);

        $this->assertEquals($date->year, 2014);
        $this->assertEquals($date->month, 12);
        $this->assertEquals($date->day, 25);
        $this->assertEquals($date->hour, 13);
        $this->assertEquals($date->minute, 4);
        $this->assertEquals($date->second, 5);
        $this->assertEquals($date->millisecond, 0.6);
    }

    //$this->assertStringMatchesFormat('%i', 'foo');
}
