<?php

namespace Khill\Lavacharts\DataTables;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\DataTables\Rows\RowFactory;
use \Khill\Lavacharts\DataTables\Columns\ColumnFactory;
use \Khill\Lavacharts\DataTables\Formats\Format;
use \Khill\Lavacharts\Exceptions\InvalidJson;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;
use \Khill\Lavacharts\Exceptions\InvalidColumnIndex;
use \Khill\Lavacharts\Exceptions\InvalidRowProperty;
use \Khill\Lavacharts\Exceptions\InvalidTimeZone;

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
class DataTable implements \JsonSerializable
{
    /**
     * Google's datatable version
     *
     * @var string
     */
    const VERSION = '0.6';

    /**
     * Google's visualization class name.
     *
     * @var string
     */
    const VIZ_CLASS = 'google.visualization.DataTable';

    /**
     * Timezone for dealing with datetime and Carbon objects.
     *
     * @var \DateTimeZone
     */
    protected $timezone;

    /**
     * The DateTime format used by Carbon when parsing string dates.
     *
     * @var string
     */
    protected $dateTimeFormat;

    /**
     * Holds all of the DataTable's column objects.
     *
     * @var array
     */
    protected $cols = [];

    /**
     * Holds all of the DataTable's row objects.
     *
     * @var array
     */
    protected $rows = [];

    /**
     * Creates a new DataTable
     *
     * @access public
     * @param  string $timezone
     * @return self
     */
    public function __construct($timezone = null)
    {
        if ($timezone === null) {
            $timezone = date_default_timezone_get();
        }

        $this->rowFactory = new RowFactory($this);

        $this->setTimezone($timezone);
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
     * @access public
     * @since  3.0.0
     * @param  mixed $v Value of the Cell
     * @param  null  $f Formatted version of the cell, as a string
     * @param  null  $p Cell specific customization options
     * @return \Khill\Lavacharts\DataTables\DataCell
     */
    public static function cell($v, $f = null, $p = null)
    {
        return new DataCell($v, $f, $p);
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

    /**
     * Sets the Timezone that Carbon will use when parsing dates
     * This will use the passed timezone, falling back to the default from php.ini,
     * and falling back from that to America/Los_Angeles
     *
     * @access public
     * @param  string $timezone
     * @return \Khill\Lavacharts\DataTables\DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidTimeZone
     */
    public function setTimezone($timezone)
    {
        try {
            $this->timezone = new \DateTimeZone($timezone);
        } catch (\Exception $e) {
            throw new InvalidTimeZone($e->getMessage());
        }

        return $this;
    }

    /**
     * Returns the current timezone used in the DataTable
     *
     * @access public
     * @since  3.0.0
     * @return \DateTimeZone
     */
    public function getTimeZone()
    {
        return $this->timezone;
    }

    /**
     * Sets the format to be used by Carbon::createFromFormat()
     *
     * This method is used to set the format to be used to parse a string
     * passed to a cell in a date column, that was parsed incorrectly by Carbon::parse()
     *
     *
     * @access public
     * @param  string $dateTimeFormat
     * @return \Khill\Lavacharts\DataTables\DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function setDateTimeFormat($dateTimeFormat)
    {
        if (Utils::nonEmptyString($dateTimeFormat) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        $this->dateTimeFormat = $dateTimeFormat;

        return $this;
    }

    /**
     * Returns the set DateTime format.
     *
     * @access public
     * @since  3.0.0
     * @return string DateTime format
     */
    public function getDateTimeFormat()
    {
        return $this->dateTimeFormat;
    }

    /**
     * Returns a clone of the DataTable
     *
     * @access public
     * @since  3.0.0
     * @return \Khill\Lavacharts\DataTables\DataTable;
     */
    public function getClone()
    {
        return clone $this;
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
     * @access public
     * @param  mixed $typeOrDescArr Column type or an array describing the column.
     * @param  string $label A label for the column. (Optional)
     * @param  \Khill\Lavacharts\DataTables\Formats\Format $format A column format object. (Optional)
     * @param  string $role A role for the column. (Optional)
     * @return \Khill\Lavacharts\DataTables\DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function addColumn($typeOrDescArr, $label = '', /*$optId = '',*/ Format $format = null, $role = '')
    {
        if (is_array($typeOrDescArr)) {
            call_user_func_array([$this, 'createColumnFromStrings'], $typeOrDescArr);
        } elseif (is_string($typeOrDescArr)) {
            $this->createColumnFromStrings($typeOrDescArr, $label, /*$optId,*/ $format, $role);
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string or array'
            );
        }

        return $this;
    }

    /**
     * Adds multiple columns to the DataTable
     *
     * @access public
     * @param  array $arrayOfColumns Array of columns to batch add to the DataTable.
     * @return \Khill\Lavacharts\DataTables\DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function addColumns($arrayOfColumns)
    {
        if (Utils::arrayIsMulti($arrayOfColumns) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'array of arrays'
            );
        }

        foreach ($arrayOfColumns as $columnArray) {
            call_user_func_array([$this, 'createColumnFromStrings'], $columnArray);
        }

        return $this;
    }


    /**
     * Supplemental function to add a boolean column with less params.
     *
     * @access public
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
        $this->cols[] = ColumnFactory::create('boolean', $label, $format, $role);

        return $this;
    }

    /**
     * Supplemental function to add a string column with less params.
     *
     * @access public
     * @param  string $label A label for the column.
     * @param  \Khill\Lavacharts\DataTables\Formats\Format $format A column format object. (Optional)
     * @param  string $role A role for the column. (Optional)
     * @return \Khill\Lavacharts\DataTables\DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidLabel
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnType
     */
    public function addStringColumn($label = '', Format $format = null, $role = '')
    {
        $this->cols[] = ColumnFactory::create('string', $label, $format, $role);

        return $this;
    }

    /**
     * Supplemental function to add a date column with less params.
     *
     * @access public
     * @param  string $label A label for the column.
     * @param  \Khill\Lavacharts\DataTables\Formats\Format $format A column format object. (Optional)
     * @param  string $role A role for the column. (Optional)
     * @return \Khill\Lavacharts\DataTables\DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidLabel
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnType
     */
    public function addDateColumn($label = '', Format $format = null, $role = '')
    {
        $this->cols[] = ColumnFactory::create('date', $label, $format, $role);

        return $this;
    }

    /**
     * Supplemental function to add a datetime column with less params.
     *
     * @access public
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
        $this->cols[] = ColumnFactory::create('datetime', $label, $format, $role);

        return $this;
    }

    /**
     * Supplemental function to add a timeofday column with less params.
     *
     * @access public
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
        $this->cols[] = ColumnFactory::create('timeofday', $label, $format, $role);

        return $this;
    }

    /**
     * Supplemental function to add a number column with less params.
     *
     * @access public
     * @param  string $label A label for the column.
     * @param  \Khill\Lavacharts\DataTables\Formats\Format $format A column format object. (Optional)
     * @param  string $role A role for the column. (Optional)
     * @return \Khill\Lavacharts\DataTables\DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidLabel
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnType
     */
    public function addNumberColumn($label = '', Format $format = null, $role = '')
    {
        $this->cols[] = ColumnFactory::create('number', $label, $format, $role);

        return $this;
    }

    /**
     * Adds a new column for defining a data role.
     *
     * @access public
     * @since  3.0.0
     * @param  string $type Type of data the column will define.
     * @param  string $role Type of role that the data will represent.
     * @return \Khill\Lavacharts\DataTables\DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnType
     */
    public function addRoleColumn($type, $role)
    {
        $this->cols[] = ColumnFactory::create($type, '', null, $role);

        return $this;
    }

    /**
     * Supplemental function to create columns from strings.
     *
     * @access protected
     * @param  string $type Type of column to create
     * @param  string $label Label for the column. (Optional)
     * @param  \Khill\Lavacharts\DataTables\Formats\Format $format A column format object. (Optional)
     * @param  string $role A role for the column. (Optional)
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    protected function createColumnFromStrings($type, $label = '', $format = null, $role = '')
    {
        $this->cols[] = ColumnFactory::create($type, $label, $format, $role);

        return $this;
    }

    /**
     * Drops a column and its data from the DataTable
     *
     * @access public
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
     * @access public
     * @param  integer $columnIndex
     * @param  \Khill\Lavacharts\DataTables\Formats\Format $format
     * @return \Khill\Lavacharts\DataTables\DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnIndex
     */
    public function formatColumn($columnIndex, Format $format)
    {
        $this->indexCheck($columnIndex);

        $this->cols[$columnIndex]->setFormat($format);

        return $this;
    }

    /**
     * Sets the format of multiple columns.
     *
     * @access public
     * @param  array $colFormatArr
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public function formatColumns($colFormatArr)
    {
        if (is_array($colFormatArr) && ! empty($colFormatArr)) {
            foreach ($colFormatArr as $index => $format) {
                if ($format instanceof Format) {
                    $this->formats[$index] = $format->toArray();
                }
            }
        }

        return $this;
    }

    /**
     * Add a row to the DataTable
     *
     * Each cell in the table is described by an array with the following properties:
     *
     * v [Optional] The cell value. The data type should match the column data type.
     * If null, the whole object should be empty and have neither v nor f properties.
     *
     * f [Optional] A string version of the v value, formatted for display. The
     * values should match, so if you specify Date(2008, 0, 1) for v, you should
     * specify "January 1, 2008" or some such string for this property. This value
     * is not checked against the v value. The visualization will not use this value
     * for calculation, only as a label for display. If omitted, a string version
     * of v will be used.
     *
     * p [Optional] An object that is a map of custom values applied to the cell.
     * These values can be of any JavaScript type. If your visualization supports
     * any cell-level properties, it will describe them; otherwise, this property
     * will be ignored. Example: p:{style: 'border: 1px solid green;'}.
     *
     *
     * Cells in the row array should be in the same order as their column descriptions
     * in cols. To indicate a null cell, you can specify null. To indicate a row
     * with null for the first two cells, you would specify [null, null, {cell_val}].
     *
     * @access public
     * @param  mixed $cellArray Array of values or DataCells.
     * @return \Khill\Lavacharts\DataTables\DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidRowDefinition
     * @throws \Khill\Lavacharts\Exceptions\InvalidRowProperty
     * @throws \Khill\Lavacharts\Exceptions\InvalidCellCount
     */
    public function addRow($cellArray = null)
    {
        if (Utils::arrayIsMulti($cellArray) === false) {
            $this->rows[] = $this->rowFactory->create($cellArray);
        } else {//TODO: timeofday cells
            $timeOfDayIndex = $this->getColumnIndexByType('timeofday');

            if ($timeOfDayIndex !== false) {
                $rowValues = $this->parseTimeOfDayRow($cellArray);
            } else {
                $rowValues = $this->parseExtendedCellArray($cellArray);
            }

            $this->rows[] = ['c' => $rowValues];
        }

        return $this;
    }

    /**
     * Adds multiple rows to the DataTable.
     *
     * @access public
     * @see    addRow()
     * @param  array $arrayOfRows
     * @return \Khill\Lavacharts\DataTables\DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidRowDefinition
     */
    public function addRows($arrayOfRows)
    {
        if (Utils::arrayIsMulti($arrayOfRows)) {
            foreach ($arrayOfRows as $row) {
                $this->addRow($row);
            }
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'array of arrays'
            );
        }

        return $this;
    }

    /**
     * Returns the rows array from the DataTable
     *
     * @access public
     * @return array
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Returns the number of rows in the DataTable
     *
     * @access public
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
     * @access public
     * @param  int $columnIndex
     * @return \Khill\Lavacharts\DataTables\Columns\Column
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnIndex
     */
    public function getColumn($columnIndex)
    {
        $this->indexCheck($columnIndex);

        return $this->cols[$columnIndex];
    }

    /**
     * Returns the column array from the DataTable
     *
     * @access public
     * @return array
     */
    public function getColumns()
    {
        return $this->cols;
    }

    /**
     * Returns the number of columns in the DataTable
     *
     * @access public
     * @return int
     */
    public function getColumnCount()
    {
        return count($this->cols);
    }

    /**
     * Returns the label of a column based on it's index.
     *
     * @access public
     * @since  3.0.0
     * @param  integer $index
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
     * @access public
     * @since  3.0.0
     * @param  integer $index
     * @return string
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnIndex
     */
    public function getColumnType($index)
    {
        return $this->getColumn($index)->getType();
    }

    /**
     * Returns the id of a column based on it's index.
     *
     * @access public
     * @since  3.0.0
     * @param  integer $index
     * @return string
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnIndex
     */
/*
    public function getColumnId($index)
    {
        return $this->getColumn($index)['id'];
    }
*/
    /**
     * Returns the types of columns currently defined.
     *
     * @since  2.5.2
     * @access public
     * @return array
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
     * @access public
     * @return array
     */
    public function getColumnLabels()
    {
        foreach ($this->cols as $column) {
            $colTypes[] = $column->getLabel();
        }

        return $colTypes;
    }

    /**
     * Returns the column number from the type of columns currently defined.
     *
     * @since  2.5.2
     * @access public
     * @param  string $type
     * @return bool|int Column index on success, false on failure.
     */
    public function getColumnIndexByType($type)
    {
        return array_search($type, $this->getColumnTypes());
    }

    /**
     * Returns the formats array from the DataTable
     *
     * @access public
     * @return array
     */
    public function getFormats()
    {
        return $this->formats;
    }

    /**
     * Boolean value if there are defined formatters
     *
     * @access public
     * @return bool
     */
    public function hasFormats()
    {
        if (count($this->formats) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Convert the DataTable to JSON
     *
     * @access public
     * @return string JSON representation of the DataTable.
     */
    public function toJson()
    {
        return json_encode($this);
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
     * Parses an extended cell definition, as and array defined with v,f,p
     *
     * @access protected
     * @param  array              $cellArray
     * @return array
     * @throws \Khill\Lavacharts\Exceptions\InvalidRowProperty
     */
    protected function parseExtendedCellArray($cellArray)
    {
        foreach ($cellArray as $prop => $value) {
            if (in_array($value, ['v', 'f', 'p']) === false) {
                throw new InvalidRowProperty;
            }

            $rowValues[] = [$prop => $value];
        }

        return $rowValues;
    }

    /**
     * Parses a timeofday row definition.
     *
     * @access protected
     * @param  array $cellArray
     * @return array
     */
    protected function parseTimeOfDayRow($cellArray)
    {
        foreach ($cellArray as $cell) {
            $rowValues[] = ['v' => $cell];
        }

        return $rowValues;
    }

    /**
     * Used to check if a number is a valid column index of the DataTable
     *
     * @param  int $columnIndex
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnIndex
     */
    protected function indexCheck($columnIndex)
    {
        if (is_int($columnIndex) === false || isset($this->cols[$columnIndex]) === false) {
            throw new InvalidColumnIndex($columnIndex, count($this->cols));
        }
    }
}
