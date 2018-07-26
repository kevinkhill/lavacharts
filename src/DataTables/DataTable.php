<?php

namespace Khill\Lavacharts\DataTables;

use DateTimeZone;
use JsonSerializable;
use Khill\Lavacharts\DataTables\Formats\Format;
use Khill\Lavacharts\DataTables\Rows\Row;
use Khill\Lavacharts\DataTables\Columns\ColumnFactory;
use Khill\Lavacharts\Exceptions\InvalidColumnDefinition;
use Khill\Lavacharts\Exceptions\InvalidColumnIndex;
use Khill\Lavacharts\Exceptions\InvalidColumnRole;
use Khill\Lavacharts\Exceptions\InvalidConfigValue;
use Khill\Lavacharts\Exceptions\InvalidDateTimeFormat;
use Khill\Lavacharts\Exceptions\InvalidTimeZone;
use Khill\Lavacharts\Support\Contracts\JsonableInterface as Jsonable;
use Khill\Lavacharts\Values\Role;
use Khill\Lavacharts\Values\StringValue;

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
 * @package   Khill\Lavacharts\DataTables
 * @since     1.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class DataTable implements Jsonable, JsonSerializable
{
    /**
     * Timezone for dealing with datetime and Carbon objects.
     *
     * @var \Khill\Lavacharts\DataTables\Columns\ColumnFactory
     */
    protected $columnFactory;

    /**
     * RowFactory for the DataTable
     *
     * @var \Khill\Lavacharts\DataTables\Rows\RowFactory
     */
    protected $rowFactory;

    /**
     * Array of the DataTable's column objects.
     *
     * @var array
     */
    protected $cols = [];

    /**
     * Array of the DataTable's row objects.
     *
     * @var array
     */
    protected $rows = [];

    /**
     * Format for Carbon to parse datetime strings.
     *
     * @var string
     */
    protected $dateTimeFormat;

    /**
     * Creates a new DataTable
     *
     * @param string $timezone Timezone to use when dealing with dates & times
     */
    public function __construct($timezone = null)
    {
        $this->columnFactory = new ColumnFactory;

        if ($timezone === null) {
            $timezone = date_default_timezone_get();
        }

        $this->setTimezone($timezone);
    }

    /**
     * Create a new DataCell for a value in a Row
     *
     * @see \Khill\Lavacharts\DataTables\DataFactory::cell
     * @deprecated 3.0.5
     *
     * @since  3.0.0
     * @param  mixed  $v Value of the Cell
     * @param  string $f Formatted version of the cell, as a string
     * @param  array  $p Cell specific customization options
     * @return \Khill\Lavacharts\DataTables\Cells\Cell
     */
    public static function cell($v, $f = '', $p = [])
    {
        return DataFactory::cell($v, $f, $p);
    }

    /**
     * Parses a string of JSON data into a DataTable.
     *
     * @deprecated 3.1.0 Use the DataFactory instead
     * @see \Khill\Lavacharts\DataTables\DataFactory::createFromJson
     *
     * @since  3.0.0
     * @param  string $jsonString JSON string to decode
     * @return \Khill\Lavacharts\DataTables\DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidJson
     */
    public static function createFromJson($jsonString)
    {
        return DataFactory::createFromJson($jsonString);
    }

    /**
     * Sets the Timezone that Carbon will use when parsing dates
     *
     * @param  string $timezone
     * @return \Khill\Lavacharts\DataTables\DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidTimeZone
     */
    public function setTimezone($timezone)
    {
        if ($this->isValidTimezone($timezone) === false) {
            throw new InvalidTimeZone($timezone);
        }

        $this->timezone = new DateTimeZone($timezone);

        return $this;
    }

    /**
     * Returns the current timezone used in the DataTable
     *
     * @since  3.0.0
     * @return \DateTimeZone
     */
    public function getTimeZone()
    {
        return $this->timezone;
    }

    /**
     * Sets the format to be used by Carbon::createFromFormat()
     * This method is used to set the format to be used to parse a string
     * passed to a cell in a date column, that was parsed incorrectly by Carbon::parse()
     *
     * @param  string $dateTimeFormat
     * @return \Khill\Lavacharts\DataTables\DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidDateTimeFormat
     */
    public function setDateTimeFormat($dateTimeFormat)
    {
        if (StringValue::isNonEmpty($dateTimeFormat) === false) {
            throw new InvalidDateTimeFormat($dateTimeFormat);
        }

        $this->dateTimeFormat = $dateTimeFormat;

        return $this;
    }

    /**
     * Returns the set DateTime format.
     *
     * @since  3.0.0
     * @return string DateTime format
     */
    public function getDateTimeFormat()
    {
        return (string) $this->dateTimeFormat;
    }

    /**
     * Returns a bare clone of the DataTable.
     *
     * This method clone the DataTable and strip the rows off. Useful when loading
     * charts via ajax, and you need the initial DataTable structure to pre-load
     * the chart's format.
     *
     * @since  3.0.0
     * @return \Khill\Lavacharts\DataTables\DataTable;
     */
    public function bare()
    {
        $clone = clone $this;

        return $clone->stripRows();
    }

    /**
     * Adds a column to the DataTable
     *
     * First signature has the following parameters:
     * type - A string with the data type of the values of the column.
     * The type can be one of the following: 'string' 'number' 'bool' 'date'
     * 'datetime' 'timeofday'.
     *
     * columnLabel - [Optional] A string with the label of the column. The column
     * label is typically displayed as part of the visualization, for example as
     *  a column header in a table, or as a legend label in a pie chart. If not
     * value is specified, an empty string is assigned.
     * optId - [Optional] A string with a unique identifier for the column. If
     * not value is specified, an empty string is assigned.
     *
     *
     * @param  mixed $typeOrColDescArr Column type or an array describing the column.
     * @param  string $label A label for the column. (Optional)
     * @param  \Khill\Lavacharts\DataTables\Formats\Format $format A column format object. (Optional)
     * @param  string $role A role for the column. (Optional)
     * @return \Khill\Lavacharts\DataTables\DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function addColumn($typeOrColDescArr, $label = '', Format $format = null, $role = '')
    {
        if (is_array($typeOrColDescArr)) {
            return call_user_func_array([$this, 'createColumnWithParams'], $typeOrColDescArr);
        }

        if (is_string($typeOrColDescArr)) {
            return $this->createColumnWithParams($typeOrColDescArr, $label, $format, $role);
        }

        throw new InvalidConfigValue(
            __FUNCTION__,
            'string|array'
        );
    }

    /**
     * Adds multiple columns to the DataTable
     *
     * @param  array $arrayOfColumns Array of columns to batch add to the DataTable.
     * @return \Khill\Lavacharts\DataTables\DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnDefinition
     */
    public function addColumns(array $arrayOfColumns)
    {
        foreach ($arrayOfColumns as $columnArray) {
            if (is_array($columnArray) === false) {
                throw new InvalidColumnDefinition($columnArray);
            }

            call_user_func_array([$this, 'createColumnWithParams'], $columnArray);
        }

        return $this;
    }

    /**
     * Supplemental function to add a boolean column with less params.
     *
     * @since  3.0.0.
     * @param  string $label A label for the column.
     * @param  \Khill\Lavacharts\DataTables\Formats\Format $format A column format object. (Optional)
     * @param  string $role A role for the column. (Optional)
     * @return \Khill\Lavacharts\DataTables\DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidLabel
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnType
     */
    public function addBooleanColumn($label = '', Format $format = null, $role = '')
    {
        return $this->createColumnWithParams('boolean', $label, $format, $role);
    }

    /**
     * Supplemental function to add a string column with less params.
     *
     * @param  string $label A label for the column.
     * @param  \Khill\Lavacharts\DataTables\Formats\Format $format A column format object. (Optional)
     * @param  string $role A role for the column. (Optional)
     * @return \Khill\Lavacharts\DataTables\DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidLabel
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnType
     */
    public function addStringColumn($label = '', Format $format = null, $role = '')
    {
        return $this->createColumnWithParams('string', $label, $format, $role);
    }

    /**
     * Supplemental function to add a date column with less params.
     *
     * @param  string $label A label for the column.
     * @param  \Khill\Lavacharts\DataTables\Formats\Format $format A column format object. (Optional)
     * @param  string $role A role for the column. (Optional)
     * @return \Khill\Lavacharts\DataTables\DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidLabel
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnType
     */
    public function addDateColumn($label = '', Format $format = null, $role = '')
    {
        return $this->createColumnWithParams('date', $label, $format, $role);
    }

    /**
     * Supplemental function to add a datetime column with less params.
     *
     * @since  3.0.0
     * @param  string $label A label for the column.
     * @param  \Khill\Lavacharts\DataTables\Formats\Format $format A column format object. (Optional)
     * @param  string $role A role for the column. (Optional)
     * @return \Khill\Lavacharts\DataTables\DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidLabel
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnType
     */
    public function addDateTimeColumn($label = '', Format $format = null, $role = '')
    {
        return $this->createColumnWithParams('datetime', $label, $format, $role);
    }

    /**
     * Supplemental function to add a timeofday column with less params.
     *
     * @since  3.0.0
     * @param  string $label A label for the column.
     * @param  \Khill\Lavacharts\DataTables\Formats\Format $format A column format object. (Optional)
     * @param  string $role A role for the column. (Optional)
     * @return \Khill\Lavacharts\DataTables\DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidLabel
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnType
     */
    public function addTimeOfDayColumn($label = '', Format $format = null, $role = '')
    {
        return $this->createColumnWithParams('timeofday', $label, $format, $role);
    }

    /**
     * Supplemental function to add a number column with less params.
     *
     * @param  string $label A label for the column.
     * @param  \Khill\Lavacharts\DataTables\Formats\Format $format A column format object. (Optional)
     * @param  string $role A role for the column. (Optional)
     * @return \Khill\Lavacharts\DataTables\DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidLabel
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnType
     */
    public function addNumberColumn($label = '', Format $format = null, $role = '')
    {
        return $this->createColumnWithParams('number', $label, $format, $role);
    }

    /**
     * Adds a new column for defining a data role.
     *
     * @since  3.0.0
     * @param  string $type    Type of data the column will define.
     * @param  string $role    Type of role that the data will represent.
     * @param  array  $options Customization of the role.
     * @return \Khill\Lavacharts\DataTables\DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnRole
     */
    public function addRoleColumn($type, $role, array $options = [])
    {
        if (Role::isValid($role) === false) {
            throw new InvalidColumnRole($role);
        }

        return $this->createColumnWithParams($type, '', null, $role, $options);
    }

    /**
     * Supplemental function to create columns from strings.
     *
     * @access protected
     * @param  string                                      $type   Type of column to create
     * @param  string                                      $label  Label for the column.
     * @param  \Khill\Lavacharts\DataTables\Formats\Format $format A column format object.
     * @param  string                                      $role   A role for the column.
     * @param  array                                       $options Extra, column specific options
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    protected function createColumnWithParams($type, $label = '', Format $format = null, $role = '', array $options = [])
    {
        $this->cols[] = $this->columnFactory->create($type, $label, $format, $role, $options);

        return $this;
    }

    /**
     * Drops a column and its data from the DataTable
     *
     * @since  3.0.0
     * @param  int $colIndex
     * @return \Khill\Lavacharts\DataTables\DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnIndex
     */
    public function dropColumn($colIndex)
    {
        if (is_int($colIndex) === false || array_key_exists($colIndex, $this->cols) === false) {
            throw new InvalidColumnIndex($colIndex, count($this->cols));
        }

        unset($this->cols[$colIndex]);

        $this->cols = array_values($this->cols);

        return $this;
    }

    /**
     * Sets the format of the column.
     *
     * @param  integer $index
     * @param  \Khill\Lavacharts\DataTables\Formats\Format $format
     * @return \Khill\Lavacharts\DataTables\DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnIndex
     */
    public function formatColumn($index, Format $format = null)
    {
        $this->indexCheck($index);

        $this->cols[$index] = $this->columnFactory->applyFormat($this->cols[$index], $format);

        return $this;
    }

    /**
     * Sets the format of multiple columns.
     *
     * @param  array $formatArray
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public function formatColumns(array $formatArray)
    {
        foreach ($formatArray as $index => $format) {
            $this->formatColumn($index, $format);
        }

        return $this;
    }

    /**
     * Add a row to the DataTable.
     *
     * A row is an array of data, that is mapped to the columns, in order.
     *
     * A column value (cell) in the row is described by a single value:
     * integer, string, date, etc.
     * OR
     * an array with the following properties to explicitly define a cell.
     * OR
     * a null to create a null row.
     *
     * Cell Properties:
     *  $cell[0] - The cell value. The data type should match the column data type.
     *
     *  $cell[1] - [Optional] A string version of the value, formatted for display. The
     *  values should match, so if you specify "2008-0-1" for [0], you should
     *  specify "January 1, 2008" or some such string for this property. This value
     *  is not checked against the [0] value. The visualization will not use this value
     *  for calculation, only as a label for display. If omitted, a string version
     *  of [0] will be used.
     *
     *  $cell[2] - [Optional] An array that is a map of custom values applied to the cell.
     *  These values can be of any JavaScript type. If your visualization supports
     *  any cell-level properties, it will describe them; otherwise, this property
     *  will be ignored. Example: [$v, $f, "style: 'border: 1px solid green;'"]
     *
     * Cells in the row array should be in the same order as their column descriptions
     * in cols. To indicate a null cell, you can specify null. To indicate a row
     * with null for the first two cells, you would specify [null, null, {cell_val}].
     *
     * @param  array|null $valueArray Array of values describing cells or null for a null row.
     * @return \Khill\Lavacharts\DataTables\DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidRowDefinition
     * @throws \Khill\Lavacharts\Exceptions\InvalidRowProperty
     * @throws \Khill\Lavacharts\Exceptions\InvalidCellCount
     */
    public function addRow(array $valueArray = null)
    {
        $this->rows[] = Row::create($this, $valueArray);

        return $this;
    }

    /**
     * Adds multiple rows to the DataTable.
     *
     * @see    addRow()
     * @param  \Khill\Lavacharts\DataTables\Rows\Row[] $arrayOfRows
     * @return \Khill\Lavacharts\DataTables\DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidRowDefinition
     */
    public function addRows(array $arrayOfRows)
    {
        /** @var array $row */
        foreach ($arrayOfRows as $row) {
            $this->addRow($row);
        }

        return $this;
    }

    /**
     * Returns the rows array from the DataTable
     *
     * @return \Khill\Lavacharts\DataTables\Rows\Row[]
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Returns the number of rows in the DataTable
     *
     * @return int
     */
    public function getRowCount()
    {
        return count($this->rows);
    }

    /**
     * Returns a column based on it's index.
     *
     * @since  3.0.0
     * @param  int $index
     * @return \Khill\Lavacharts\DataTables\Columns\Column
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnIndex
     */
    public function getColumn($index)
    {
        $this->indexCheck($index);

        return $this->cols[$index];
    }

    /**
     * Returns the column array from the DataTable
     *
     * @return \Khill\Lavacharts\DataTables\Columns\Column[]
     */
    public function getColumns()
    {
        return $this->cols;
    }

    /**
     * Returns the columns whos type match the given value.
     *
     * @since  3.0.0
     * @param  string $type
     * @return array
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnType
     */
    public function getColumnsByType($type)
    {
        ColumnFactory::isValidType($type);

        $indices = [];

        foreach ($this->cols as $index => $column) {
            if ($type === $column->getType()) {
                $indices[$index] = $column;
            }
        }

        return $indices;
    }

    /**
     * Returns the number of columns in the DataTable
     *
     * @return int
     */
    public function getColumnCount()
    {
        return count($this->cols);
    }

    /**
     * Returns the label of a column based on it's index.
     *
     * @since  3.0.0
     * @param  int $index
     * @return string
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnIndex
     */
    public function getColumnLabel($index)
    {
        return $this->getColumn($index)->getLabel();
    }

    /**
     * Returns the type of a column based on it's index.
     *
     * @since  3.0.0
     * @param  int $index
     * @return string
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnIndex
     */
    public function getColumnType($index)
    {
        return $this->getColumn($index)->getType();
    }

    /**
     * Returns the types of columns currently defined.
     *
     * @since  2.5.2
     * @return string[]
     */
    public function getColumnTypes()
    {
        foreach ($this->cols as $column) {
            $colTypes[] = $column->getType();
        }

        return $colTypes;
    }

    /**
     * Returns the labels of columns currently defined.
     *
     * @since  3.0.0
     * @return string[]
     */
    public function getColumnLabels()
    {
        foreach ($this->cols as $column) {
            $colTypes[] = $column->getLabel();
        }

        return $colTypes;
    }

    /**
     * Returns the formatted columns in an array from the DataTable
     *
     * @since  3.0.0
     * @return \Khill\Lavacharts\DataTables\Columns\Column[]
     */
    public function getFormattedColumns()
    {
        $columns = [];

        foreach ($this->cols as $index => $column) {
            if ($column->isFormatted()) {
                $columns[$index] = $column;
            }
        }

        return $columns;
    }

    /**
     * Boolean value if there are any formatted columns
     *
     * @return bool
     */
    public function hasFormattedColumns()
    {
        return count($this->getFormattedColumns()) > 0;
    }

    /**
     * Convert the DataTable to JSON
     *
     * Will include formats if defined
     *
     * @return string JSON representation of the DataTable.
     */
    public function toJson()
    {
        if ($this->hasFormattedColumns()) {
            $formats = [];

            foreach ($this->getFormattedColumns() as $index => $column) {
                $format = $column->getFormat();

                $formats[] = [
                    'index'  => $index,
                    'type'   => $format->getType(),
                    'config' => $format
                ];
            }

            return json_encode([
                'data' => $this,
                'formats' => $formats
            ]);
        } else {
            return json_encode($this);
        }
    }

    /**
     * Custom serialization of the DataTable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'cols' => $this->cols,
            'rows' => $this->rows,
        ];
    }

    /**
     * Used to isNonEmpty if a number is a valid column index of the DataTable
     *
     * @access protected
     * @param  int $index
     * @return void
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnIndex
     */
    protected function indexCheck($index)
    {
        if (is_int($index) === false || isset($this->cols[$index]) === false) {
            throw new InvalidColumnIndex($index, count($this->cols));
        }
    }

    /**
     * Strips all the data (rows) from the DataTable
     *
     * @access protected
     * @since  3.0.0
     * @return self
     */
    protected function stripRows()
    {
        $this->rows = [];

        return $this;
    }

    /**
     * Checks the variable against the built-in list of valid php timezones.
     *
     * Returns true if in the list, otherwise false.
     *
     * @param string $tz
     * @return bool
     */
    protected function isValidTimezone($tz) {
        if (! is_string($tz)) {
            return false;
        }

        $timezones = timezone_identifiers_list();
        $timezones = array_map('strtolower',$timezones);

        return in_array(strtolower($tz), $timezones, true);
    }

}
