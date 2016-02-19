<?php

namespace Khill\Lavacharts\Tests;

abstract class ProvidersTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Namespace for Mocks
     */
    const NS = '\Khill\Lavacharts';

    /**
     * Partial DataTable for use throughout various tests
     */
    protected $partialDataTable;

    protected $columnTypes = [
        'boolean',
        'number',
        'string',
        'date',
        'datetime',
        'timeofday'
    ];

    public function setUp()
    {
        parent::setUp();

        $this->partialDataTable = \Mockery::mock(self::NS.'\DataTables\DataTable')->makePartial();
    }

    public function getMockLabel($label)
    {
        return \Mockery::mock(self::NS.'\Values\Label', [$label])
                       ->shouldReceive('__toString')
                       ->andReturn($label)
                       ->getMock();
    }

    public function getMockElemId($elemId)
    {
        return \Mockery::mock(self::NS.'\Values\ElementId', [$elemId])
                       ->shouldReceive('__toString')
                       ->andReturn($elemId)
                       ->getMock();
    }

    /**
     * Uses reflection to retrieve private member variables from objects.
     *
     * @param  object $obj
     * @param  string $prop
     * @return mixed
     */
    public function getPrivateProperty($obj, $prop)
    {
        $refObj = new \ReflectionClass($obj);
        $refProp = $refObj->getProperty($prop);
        $refProp->setAccessible(true);

        return $refProp->getValue($obj);
    }

    public function columnTypeProvider()
    {
        return array_map(function ($columnType) {
            return [$columnType];
        }, $this->columnTypes);
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

    public function nonCarbonOrDateStringProvider()
    {
        return [
            [9],
            [14.6342],
            [true],
            [false],
            [new \stdClass()]
        ];
    }

    public function nonCarbonOrDateOrEmptyArrayProvider()
    {
        return [
            ['cheese'],
            [9],
            [14.6342],
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
            [null],
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
