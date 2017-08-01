<?php

namespace Khill\Lavacharts\DataTables;

use Closure;
use Khill\Lavacharts\DataTables\Cells\Cell;
use Khill\Lavacharts\DataTablePlus\DataTablePlus;
use Khill\Lavacharts\Exceptions\InvalidJson;
use Khill\Lavacharts\Support\Contracts\DataInterface;
use ReflectionClass;

/**
 * Class DataFactory
 *
 * The DataFactory is used to create new DataTables and DataCells in many different
 * ways.
 *
 *
 * @package   Khill\Lavacharts\DataTables
 * @since     3.1.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class DataFactory
{
    /**
     * Creates a new DataTable
     *
     * There are four signatures to this method:
     *
     * - 0 parameters: An empty DataTable is created with the default options.
     *
     * - 1 parameter: The argument must be an array, overriding the default options.
     *
     * - 2 parameters: The first arg is now considered to be Column definitions that
     *   will be passed into DataTable#addColumns() and the second arg is options.
     *
     * - 3 parameters: The first arg is now considered to be Row definitions that
     *   will be passed into DataTable#addRows(), the second arg now Column definitions
     *   and the last arg is options.
     *
     * @since  4.0.0 Variable arguments
     * @param  array $args
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public static function DataTable(...$args)
    {
        $datatable = self::emptyDataTable();

        if (count($args) == 1) {
            if (is_array($args[0])) {
                $datatable->setOptions($args[0]);
            }
        }

        if (count($args) == 2) {
            if (is_array($args[1])) {
                $datatable->setOptions($args[1]);
            }

            if (is_array($args[0])) {
                $datatable->addColumns($args[0]);
            }

        }

        if (count($args) == 3) {
            if (is_array($args[2])) {
                $datatable->setOptions($args[2]);
            }

            if (is_array($args[0])) {
                $datatable->addColumns($args[0]);
            }

            if (is_array($args[1])) {
                $datatable->addRows($args[1]);
            }

            if ($args[1] instanceof Closure) {
                $datatable->addRows($args[1]());
            }

        }

        return $datatable;
    }

    /**
     * Create a new instance of a JoinedDataTable.
     *
     * @param DataTable $data1
     * @param DataTable $data2
     * @param array     $options
     * @return JoinedDataTable
     */
    public static function JoinedDataTable(DataTable $data1, DataTable $data2, array $options = [])
    {
        return new JoinedDataTable($data1, $data2, $options);
    }

    /**
     * Create a new, empty DataTable.
     *
     * This method will create an empty DataTable, with or without a timezone.
     *
     * @param array $options
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public static function emptyDataTable(array $options = [])
    {
        $datatable = DataTablePlus::class;

        if (! class_exists($datatable)) {
            $datatable = DataTable::class;
        }

        return new $datatable($options);
    }

    /**
     * Parse an array to create a full DataTable.
     *
     * Replicating the functionality of Google's javascript version
     * of automatic datatable creation from an array of column headings
     * and data.
     * Passing true as the second parameter will bypass column labeling
     * and treat the first row as data.
     *
     * @param  array $tableArray Array of arrays containing column labels and data.
     * @param  bool  $firstRowIsData If true, the first row is treated as data, not column labels.
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public static function arrayToDataTable($tableArray, $firstRowIsData = false)
    {
        $datatable   = new DataTable();
        $columnCount = count($tableArray[0]);

        if ($firstRowIsData === false) {
            $columnLabels = array_shift($tableArray);
        }

        $rowCount = count($tableArray);

        for ($i = 0; $i < $columnCount; $i++) {
            $columnType = gettype($tableArray[0][$i]);

            while ($columnType === null) {
                $columnType = gettype($tableArray[rand(1, $rowCount)][$i]);
            }

            switch ($columnType) {
                case 'integer':
                case 'float':
                case 'double':
                    $column = ['number'];
                    break;

                case 'string':
                default:
                    $column = ['string'];
                    break;
            }

            if (isset($columnLabels[$i])) {
                $column[] = $columnLabels[$i];
            }

            $datatable->addColumn($column);
        }

        $datatable->addRows($tableArray);

        return $datatable;
    }

    /**
     * Parses a string of JSON data into a DataTable.
     *
     * Most google examples can be passed to this method with some tweaks.
     * PHP requires that only double quotes are used on identifiers.
     * For example:
     *  - {label: 'Team'} would be invalid
     *  - {"label": "Team"} would be accepted.
     *
     * @param  string $jsonString JSON string to decode
     * @return \Khill\Lavacharts\DataTables\DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidJson
     */
    public static function createFromJson($jsonString)
    {
        $decodedJson = json_decode($jsonString, true);

        if ($decodedJson === null) {
            throw new InvalidJson;
        }

        $datatable = new DataTable();

        foreach ($decodedJson['cols'] as $column) {
            $datatable->addColumn($column['type'], $column['label']);
        }

        foreach ($decodedJson['rows'] as $row) {
            $rowData = [];

            foreach ($row['c'] as $cell) {
                $rowData[] = $cell['v'];
            }

            $datatable->addRow($rowData);
        }

        return $datatable;
    }
}
