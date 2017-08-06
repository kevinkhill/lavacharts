<?php

namespace Khill\Lavacharts\Tests;

use Khill\Lavacharts\Charts\ChartFactory;
use Khill\Lavacharts\DataTables\Columns\Column;
use Khill\Lavacharts\DataTables\Columns\ColumnFactory;
use Khill\Lavacharts\DataTables\DataTable;
use PHPUnit\Framework\TestCase;

abstract class ProvidersTestCase extends TestCase
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

        /**
         * Setting timezone to avoid warning from Carbon
         */
        date_default_timezone_set('America/Los_Angeles');

        $this->partialDataTable = \Mockery::mock(DataTable::class)->makePartial();
    }

    /**
     * Checks if a string contains another string
     *
     * @param $haystack
     * @param $needle
     */
    public function assetStringHasString($haystack, $needle)
    {
        $this->assertTrue(strpos($haystack, $needle) !== false);
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
        $refObj = new \ReflectionProperty($obj, $prop);
        $refObj->setAccessible(true);

        return $refObj->getValue($obj);
    }

    /**
     * Returns all available column types, labeled as self for testing.
     *
     * @return array
     */
    public function columnTypeProvider()
    {
        $types = [];

        foreach (Column::TYPES as $columnType) {
            $types[$columnType] = [$columnType];
        }

        return $types;
    }

    /**
     * Returns all available chart types, labeled as self for testing.
     *
     * @return array
     */
    public function chartTypeProvider()
    {
        $types = [];

        foreach (ChartFactory::TYPES as $chartType) {
            $types[$chartType] = [$chartType];
        }

        return $types;
    }

    public function nonStringOrIntProvider()
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

    public function nonStringOrNullProvider()
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
