<?php

namespace Khill\Lavacharts\Tests;

use Khill\Lavacharts\Charts\ChartFactory;
use Khill\Lavacharts\Dashboards\Filter;
use Khill\Lavacharts\DataTables\Columns\Column;
use Khill\Lavacharts\DataTables\Columns\Format;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

abstract class ProvidersTestCase extends TestCase
{
    const CHART_NAMESPACE = '\\Khill\\Lavacharts\\Charts\\';

    protected function getPath($path)
    {
        return realpath(__DIR__ . '/../' . $path);
    }

    /**
     * Checks if a string contains another string
     *
     * @param $haystack
     * @param $needle
     */
    public function assertStringContains($haystack, $needle)
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
        $refObj = new ReflectionProperty($obj, $prop);
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

    /**
     * Returns all available format types, labeled as self for testing.
     *
     * @return array
     */
    public function formatTypeProvider()
    {
        $types = [];

        foreach (Format::TYPES as $formatType) {
            $types[$formatType] = [$formatType];
        }

        return $types;
    }

    /**
     * Returns all available format types, without 'Format', labeled as self for testing.
     *
     * @return array
     */
    public function shortnameFormatTypeProvider()
    {
        $types = [];

        foreach (Format::TYPES as $formatType) {
            $formatType = str_replace('Format', '', $formatType);

            $types[$formatType] = [$formatType];
        }

        return $types;
    }

    /**
     * Returns all available filter types, labeled as self for testing.
     *
     * @return array
     */
    public function filterTypeProvider()
    {
        $types = [];

        foreach (Filter::TYPES as $filterType) {
            $types[$filterType] = [$filterType];
        }

        return $types;
    }

    /**
     * Returns all available filter types, without 'Filter', labeled as self for testing.
     *
     * @return array
     */
    public function shortnameFilterTypeProvider()
    {
        $types = [];

        foreach (Filter::TYPES as $filterType) {
            $filterType = str_replace('Filter', '', $filterType);

            $types[$filterType] = [$filterType];
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
