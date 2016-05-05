<?php

namespace Khill\Lavacharts\Tests;

use Khill\Lavacharts\Charts\ChartFactory;
use Khill\Lavacharts\DataTables\Columns\ColumnFactory;

define('DATATABLE_NS', "\\Khill\\Lavacharts\\DataTables\\");

abstract class ProvidersTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Partial DataTable for use throughout various tests
     *
     * @var \Khill\Lavacharts\DataTables\DataTable
     */
    protected $partialDataTable;

    public function setUp()
    {
        parent::setUp();

        $this->partialDataTable = \Mockery::mock('Khill\Lavacharts\DataTables\DataTable')->makePartial();
    }

    /**
     * Uses reflection to retrieve private member variables from objects.
     *
     * @param  object $obj
     * @param  string $prop
     * @return mixed
     */
    public function inspect($obj, $prop)
    {
        $refObj = new \ReflectionClass($obj);
        $refProp = $refObj->getProperty($prop);
        $refProp->setAccessible(true);

        return $refProp->getValue($obj);
    }

    /**
     * DataProvider for the column types
     *
     * @return array
     */
    public function columnTypeProvider()
    {
        return array_map(function ($columnType) {
            return [$columnType];
        }, ColumnFactory::$types);
    }

    /**
     * DataProvider for the chart types
     *
     * @return array
     */
    public function chartTypeProvider()
    {
        return array_map(function ($chartType) {
            return [$chartType];
        }, ChartFactory::getChartTypes());
    }


    /**
     * Create a mock Label with the given string
     *
     * @param  string $label
     * @return \Mockery\Mock
     */
    public function getMockLabel($label)
    {
        return \Mockery::mock('\Khill\Lavacharts\Values\Label', [$label])->makePartial();
    }


    /**
     * Create a mock DataTable
     *
     * @return \Mockery\Mock
     */
    public function getMockDataTable()
    {
        return \Mockery::mock('Khill\Lavacharts\DataTables\DataTable')->makePartial();
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
