<?php

namespace Khill\Lavacharts\DataTables;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\DataTables\Cells\Cell;
use \Khill\Lavacharts\DataTables\Formats\Format;
use \Khill\Lavacharts\DataTables\Rows\RowFactory;
use \Khill\Lavacharts\DataTables\Columns\ColumnFactory;
use \Khill\Lavacharts\Exceptions\InvalidJson;
use \Khill\Lavacharts\Exceptions\InvalidTimeZone;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;
use \Khill\Lavacharts\Exceptions\InvalidColumnIndex;
use \Khill\Lavacharts\Exceptions\InvalidRowProperty;
use \Khill\Lavacharts\Exceptions\InvalidColumnType;

/**
 * The DataTable object is used to hold the data passed into a visualization.
 *
 * A DataTable is a basic two-dimensional table. All data in each column must
 * have the same data type. Each column has a descriptor that includes its data
 * type, a label for that column (which might be displayed by a visualization),
 * and an ID, which can be used to refer to a specific column (as an alternative
 * to using column indexes). The DataTable object also supports a map of
 * arbitrary properties assigned to a specific value, a row, a column, or the
 * whole DataTable. Visualizations can use these to support additional features;
 * for example, the Table visualization uses custom properties to let you assign
 * arbitrary class names or styles to individual cells.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage DataTables
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class DataFactory
{
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
     * @access public
     * @since  3.0.0
     * @param  mixed  $v Value of the Cell
     * @param  string $f Formatted version of the cell, as a string
     * @param  array  $p Cell specific customization options
     * @return \Khill\Lavacharts\DataTables\Cells\Cell
     */
    public static function cell($v, $f = '', $p = [])
    {
        return new Cell($v, $f, $p);
    }

    public static function createDataTable($timezone = null)
    {
        $datatable = '\Khill\Lavacharts\DataTablePlus\DataTablePlus';

        if (class_exists($datatable) === false) {
            $datatable = '\Khill\Lavacharts\DataTables\DataTable';
        }

        if ($timezone === null) {
            return new $datatable($timezone);
        } else {
            return new $datatable;
        }
    }

    /**
     * Parse an array to create a full DataTable.
     *
     * Replicating the functionality of Google's javascript version
     * of automatic datatable creation from an array of column headings
     * and data.
     *
     * @since 3.0.1
     * @param array $tableArray
     */
    public static function arrayToDataTable($tableArray)
    {
        $datatable = new DataTable();

        $columnLabels = array_shift($tableArray);
        $columnCount  = count($columnLabels);
        $rowCount     = count($tableArray);

        for ($i = 0; $i < $columnCount; $i++) {
            $columnType = gettype($tableArray[0][$i]);

            while ($columnType === null) {
                $columnType = gettype($tableArray[rand(1, $rowCount)][$i]);
            }

            switch ($columnType) {
                case 'integer':
                case 'float':
                case 'double':
                    $datatable->addColumn('number', $columnLabels[$i]);
                    break;

                case 'string':
                default:
                    $datatable->addColumn('string', $columnLabels[$i]);
                    break;
            }
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
     * @access public
     * @since  3.0.0
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
