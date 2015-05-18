<?php namespace Khill\Lavacharts\Tests;

//use Khill\Lavacharts\Lavacharts;
use \Mockery as m;

abstract class ProvidersTestCase extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockDataTable = m::mock('Khill\Lavacharts\Configs\DataTable');
    }

    public function nonIntOrPercentProvider()
    {
        return [
            [3.2],
            [true],
            [false],
            [[]],
            [new \stdClass]
        ];
    }

    public function nonCarbonOrDateOrEmptyArrayProvider()
    {
        return [
            ['cheese'],
            [9],
            [1.2],
            [true],
            [false],
            [new \stdClass()]
        ];
    }

    public function nonConfigObjectProvider()
    {
        return [
            ['stringy'],
            [9],
            [1.2],
            [true],
            [false],
            [[]],
            [new \stdClass()]
        ];
    }

    public function nonStringProvider()
    {
        return [
            [9],
            [1.2],
            [true],
            [false],
            [[]],
            [new \stdClass()]
        ];
    }

    public function nonBoolProvider()
    {
        return [
            ['Imastring'],
            [9],
            [1.2],
            [[]],
            [new \stdClass()]
        ];
    }

    public function nonIntProvider()
    {
        return [
            ['Imastring'],
            [1.2],
            [true],
            [false],
            [[]],
            [new \stdClass()]
        ];
    }

    public function nonFloatProvider()
    {
        return [
            ['Imastring'],
            [9],
            [true],
            [false],
            [[]],
            [new \stdClass()]
        ];
    }

    public function nonNumericProvider()
    {
        return [
            ['Imastring'],
            [true],
            [false],
            [[]],
            [new \stdClass()]
        ];
    }

    public function nonArrayProvider()
    {
        return [
            ['Imastring'],
            [9],
            [1.2],
            [true],
            [false],
            [new \stdClass()]
        ];
    }
}
