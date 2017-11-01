<?php

namespace Khill\Lavacharts\DataTables;

use Khill\Lavacharts\DataTables\Cells\Cell;
use Khill\Lavacharts\Exceptions\InvalidJson;

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
     * This method will create an empty DataTable, with a timezone if
     * passed a string as the first parameter or with no timezone if passed nothing.
     *
     * If the first parameter is an array, then the array will be attempted to be
     * used as columns for a new DataTable.
     *
     * If given an array for the second parameter, then it will be interpreted as
     * row definitions.
     *
     * @param  mixed  $columns  Array of columns or timezone
     * @param  array  $rows     Array of rows
     * @param  string $timezone Timezone to use while using Carbon
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public static function DataTable($columns = null, array $rows = [], $timezone = null)
    {
        if ($columns === null || gettype($columns) === 'string') {
            $timezone = $columns;
        }

        $datatable = self::emptyDataTable($timezone);

        if (is_array($columns)) {
            $datatable->addColumns($columns);
        }

        if (is_array($rows)) {
            $datatable->addRows($rows);
        } else if ($rows instanceof \Closure) {
            $datatable->addRows($rows());
        }

        return $datatable;
    }

    /**
     * Create a new, empty DataTable.
     *
     * This method will create an empty DataTable, with or without a timezone.
     *
     * @param  string $timezone Timezone to use while using Carbon
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    private static function emptyDataTable($timezone)
    {
        $datatable = '\Khill\Lavacharts\DataTablePlus\DataTablePlus';

        if (class_exists($datatable) === false) {
            $datatable = '\Khill\Lavacharts\DataTables\DataTable';
        }

        return new $datatable($timezone);
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
            if (isset($column['label'])) {
                $label = $column['label'];
            } else {
                $label = '';
            }

            $datatable->addColumn($column['type'], $label);
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

    /**
     * Create a new DataCell for a value in a Row
     *
     * v: The cell value. The data type should match the column data type.
     * If null, the whole object should be empty and have neither v nor f properties.
     *
     * f: [Optional] A string version of the v value, formatted for display. The
     * values should match, so if you specify Date(2008, 0, 1) for v, you should
     * specify "January 1, 2008" or some such string for this property. This value
     * is not checked against the v value. The visualization will not use this value
     * for calculation, only as a label for display. If omitted, a string version
     * of v will be used.
     *
     * p: [Optional] An object that is a map of custom values applied to the cell.
     * These values can be of any JavaScript type. If your visualization supports
     * any cell-level properties, it will describe them; otherwise, this property
     * will be ignored. Example: p:{style: 'border: 1px solid green;'}.
     *
     *
     * @param  mixed  $v Value of the Cell
     * @param  string $f Formatted version of the cell, as a string
     * @param  array  $p Cell specific customization options
     * @return \Khill\Lavacharts\DataTables\Cells\Cell
     */
    public static function cell($v, $f = '', $p = [])
    {
        return new Cell($v, $f, $p);
    }
}
