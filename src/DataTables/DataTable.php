<?php

namespace Khill\Lavacharts\DataTables;

use Carbon\Carbon;
use DateTimeZone;
use Exception;
use Khill\Lavacharts\Dashboards\Filter;
use Khill\Lavacharts\DataTables\Cells\Cell;
use Khill\Lavacharts\DataTables\Cells\DateCell;
use Khill\Lavacharts\DataTables\Columns\Column;
use Khill\Lavacharts\DataTables\Columns\Format;
use Khill\Lavacharts\DataTables\Columns\Role;
use Khill\Lavacharts\Exceptions\InvalidArgumentException;
use Khill\Lavacharts\Exceptions\InvalidCellCount;
use Khill\Lavacharts\Exceptions\InvalidColumnDefinition;
use Khill\Lavacharts\Exceptions\InvalidColumnIndex;
use Khill\Lavacharts\Exceptions\InvalidDateTimeFormat;
use Khill\Lavacharts\Exceptions\UndefinedColumnsException;
use Khill\Lavacharts\Support\Contracts\Arrayable;
use Khill\Lavacharts\Support\Contracts\Customizable;
use Khill\Lavacharts\Support\Contracts\DataInterface;
use Khill\Lavacharts\Support\Contracts\Javascriptable;
use Khill\Lavacharts\Support\Contracts\Jsonable;
use Khill\Lavacharts\Support\Traits\ArrayToJsonTrait as ArrayToJson;
use Khill\Lavacharts\Support\Traits\HasOptionsTrait as HasOptions;
use Khill\Lavacharts\Support\Traits\ToJavascriptTrait as ToJavascript;
use Khill\Lavacharts\Support\StringValue;

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
 * @package       Khill\Lavacharts\DataTables
 * @since         1.0.0
 * @author        Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link          http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link          http://lavacharts.com                   Official Docs Site
 * @license       http://opensource.org/licenses/MIT      MIT
 */
class DataTable implements DataInterface, Customizable, Arrayable, /*Javascriptable,*/ Jsonable
{
    use HasOptions, ArrayToJson/*, ToJavascript*/;

    /**
     * Column types that use dates and/or times
     */
    const DATE_TIME_COLUMNS = [
        'date',
        'time',
        'datetime',
        'timeofday',
    ];

    /**
     * Array of the DataTable's column objects.
     *
     * @var Column[]
     */
    protected $columns = [];

    /**
     * Array of the DataTable's row objects.
     *
     * @var Row[]
     */
    protected $rows = [];

    /**
     * Create a new DataTable using the DataFactory
     *
     * @param array $args
     * @return DataTable
     * @internal param array $options
     */
    public static function create(...$args)
    {
        return DataFactory::DataTable($args);
    }

    /**
     * Create a new DataTable from an array using the DataFactory
     *
     * @param array $tableArray
     * @param bool  $firstRowIsData
     * @return DataTable
     */
    public static function fromArray($tableArray, $firstRowIsData = false)
    {
        return DataFactory::arrayToDataTable($tableArray, $firstRowIsData);
    }

    /**
     * DataTable constructor
     *
     * Default options will be set if none are passed.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->initOptions($options);

        if (isset($options['timezone'])) {
            try {
                $timezone = (new DateTimeZone($options['timezone']))->getName();
            } catch(Exception $e) {
                $timezone = date_default_timezone_get();

                trigger_error(
                    $e->getMessage().', using the default "'.$timezone.'"',
                    E_USER_NOTICE
                );
            }

            $this->options->set('timezone', $timezone);
        }
    }

    /**
     * Create a new JoinedDataTable by joining the current table to another with options.
     *
     * @param $datatable
     * @param $options
     * @return \Khill\Lavacharts\DataTables\JoinedDataTable
     */
    public function join($datatable, $options)
    {
        return new JoinedDataTable($this, $datatable, $options);
    }

    /**
     * Get the DataTable as an array.
     *
     * @since  4.0.0
     * @return array
     */
    public function toArray()
    {
        return [
            'cols' => $this->columns,
            'rows' => $this->rows,
        ];
    }

    /**
     * @inheritdoc
     */
    public function toJsDataTable() //TODO: re-think this.
    {
        return $this->toJavascript();
    }

    /**
     * Convert the DataTable to JSON
     *
     * Will include formats if defined
     *
     * @since 4.0.0 A boolean can be passed to disable the output of formatters.
     * @param bool $withFormats
     * @return string JSON representation of the DataTable.
     */
    public function toJson(/*$withFormats = false*/)
    {
//TODO: Figure out a better way to get the formats to the lava.js module. We should output a pure DataTable from toJson() to be compatable with the JSON Schema
//        if ($this->hasFormattedColumns() && $withFormats === true) {
//            return json_encode([
//                'data'    => $this,
//                'formats' => $this->getFormattedColumns(),
//            ]);
//        }

        return json_encode($this);
    }

    /**
     * Return a format string that will be used by vsprintf to convert the
     * extending class to javascript.
     *
     * @return string
     */
//    public function getJavascriptFormat()
//    {
//        return 'new google.visualization.DataTable(%s)';
//    }

    /**
     * Return an array of arguments to pass to the format string provided
     * by getJavascriptFormat().
     *
     * These variables will be used with vsprintf, and the format string
     * to convert the extending class to javascript.
     *
     * @return array
     */
//    public function getJavascriptSource()
//    {
//        return [
//            $this->toJson(false) //TODO: no no no
//        ];
//    }

    /**
     * Returns the current timezone used in the DataTable
     *
     * @since  3.0.0
     * @return \DateTimeZone
     */
    public function getTimeZone()
    {
        return $this->options->timezone;
    }

    /**
     * Sets the Timezone that Carbon will use when parsing dates
     *
     * @param  string $timezone
     * @return self
     */
    public function setTimezone($timezone)
    {
        $this->options->set('timezone', (new DateTimeZone($timezone))->getName());

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
        return $this->options->datetime_format;
    }

    /**
     * Sets the format to be used by Carbon::createFromFormat()
     * This method is used to set the format to be used to parse a string
     * passed to a cell in a date column, that was parsed incorrectly by Carbon::parse()
     *
     * @param  string $dateTimeFormat
     * @return DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidDateTimeFormat
     */
    public function setDateTimeFormat($dateTimeFormat)
    {
        if (StringValue::isNonEmpty($dateTimeFormat) === false) {
            throw new InvalidDateTimeFormat($dateTimeFormat);
        }

        $this->options->set('datetime_format', $dateTimeFormat);

        return $this;
    }

    /**
     * Returns a bare clone of the DataTable.
     *
     * This method clone the DataTable and strip the rows off. Useful when loading
     * charts via ajax, and you need the initial DataTable structure to pre-load
     * the chart's format.
     *
     * @since  3.0.0
     * @return DataTable
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
     * @param string|array $type    Column type or an array describing the column.
     * @param string       $label   A label for the column. (Optional)
     * @param Format       $format  A column format object. (Optional)
     * @param string       $role    A role for the column. (Optional)
     * @param array        $options
     * @return DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidArgumentException
     */
    public function addColumn(
        $type,
        $label = '',
        Format $format = null,
        $role = null,
        array $options = []
    ) {
        if (is_array($type)) {
            return call_user_func_array([$this, 'createColumnWithParams'], $type);
        }

        if (is_string($type)) {
            return $this->createColumnWithParams($type, $label, $format, $role, $options);
        }

        throw new InvalidArgumentException($type, 'string or array.');
    }

    /**
     * Adds multiple columns to the DataTable
     *
     * @param  array[] $arrayOfColumns Array of columns to batch add to the DataTable.
     * @return DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnDefinition
     */
    public function addColumns(array $arrayOfColumns)
    {
        //TODO: allow assoc array of type => label
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
     * @param  string $label  A label for the column.
     * @param  Format $format A column format object. (Optional)
     * @param  string $role   A role for the column. (Optional)
     * @return DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnType
     */
    public function addBooleanColumn($label = '', Format $format = null, $role = null)
    {
        return $this->createColumnWithParams('boolean', $label, $format, $role);
    }

    /**
     * Supplemental function to add a string column with less params.
     *
     * @param  string $label  A label for the column.
     * @param  Format $format A column format object. (Optional)
     * @param  string $role   A role for the column. (Optional)
     * @return DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnType
     */
    public function addStringColumn($label = '', Format $format = null, $role = null)
    {
        return $this->createColumnWithParams('string', $label, $format, $role);
    }

    /**
     * Supplemental function to add a date column with less params.
     *
     * @param  string $label  A label for the column.
     * @param  Format $format A column format object. (Optional)
     * @param  string $role   A role for the column. (Optional)
     * @return DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnType
     */
    public function addDateColumn($label = '', Format $format = null, $role = null)
    {
        return $this->createColumnWithParams('date', $label, $format, $role);
    }

    /**
     * Supplemental function to add a datetime column with less params.
     *
     * @since  3.0.0
     * @param  string $label  A label for the column.
     * @param  Format $format A column format object. (Optional)
     * @param  string $role   A role for the column. (Optional)
     * @return DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnType
     */
    public function addDateTimeColumn($label = '', Format $format = null, $role = null)
    {
        return $this->createColumnWithParams('datetime', $label, $format, $role);
    }

    /**
     * Supplemental function to add a timeofday column with less params.
     *
     * @since  3.0.0
     * @param  string $label  A label for the column.
     * @param  Format $format A column format object. (Optional)
     * @param  string $role   A role for the column. (Optional)
     * @return DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnType
     */
    public function addTimeOfDayColumn($label = '', Format $format = null, $role = null)
    {
        return $this->createColumnWithParams('timeofday', $label, $format, $role);
    }

    /**
     * Supplemental function to add a number column with less params.
     *
     * @param  string $label  A label for the column.
     * @param  Format $format A column format object. (Optional)
     * @param  string $role   A role for the column. (Optional)
     * @return DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnType
     */
    public function addNumberColumn($label = '', Format $format = null, $role = null)
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
     * @return DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnRole
     */
    public function addRoleColumn($type, $role, array $options = [])
    {
        $role = Role::verify($role);

        return $this->createColumnWithParams($type, '', null, $role, $options);
    }

    /**
     * Supplemental function to create columns from strings.
     *
     * @access protected
     * @param  string $type    Type of column to create
     * @param  string $label   Label for the column.
     * @param  Format $format  A column format object.
     * @param  string $role    A role for the column.
     * @param  array  $options Extra, column specific options
     * @return DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnDefinition
     */
    protected function createColumnWithParams(
        $type,
        $label = '',
        Format $format = null,
        $role = null,
        array $options = []
    ) {
        $builder = Column::createBuilder();

        Column::isValidType($type);

        $builder->setType($type);
        $builder->setLabel($label);
        $builder->setFormat($format);
        $builder->setRole($role);
        $builder->setOptions($options);

        $this->pushColumn(
            $builder->getColumn()
        );

        return $this;
    }

    /**
     * Drops a column and its data from the DataTable
     *
     * @since  3.0.0
     * @param  int $index
     * @return DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnIndex
     */
    public function dropColumn($index)
    {
        $this->indexCheck($index);

        unset($this->columns[$index]);

        $this->columns = array_values($this->columns);

        return $this;
    }

    /**
     * Sets the format of the column.
     *
     * @param  integer                                     $index
     * @param  \Khill\Lavacharts\DataTables\Columns\Format $format
     * @return DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnIndex
     */
    public function formatColumn($index, Format $format)
    {
        $this->getColumn($index)->setFormat($format);

        return $this;
    }

    /**
     * Sets the format of multiple columns.
     *
     * @param  array $formatArray
     * @return DataTable
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
     *
     * @param  array|Row|null $values Array of values describing cells or null for a null row.
     * @return DataTable
     * @throws \Khill\Lavacharts\Exceptions\InvalidArgumentException
     * @throws \Khill\Lavacharts\Exceptions\InvalidCellCount
     * @throws \Khill\Lavacharts\Exceptions\UndefinedColumnsException
     */
    public function addRow($values = null)
    {
        $newRow      = Row::createEmpty();
        $columnCount = $this->getColumnCount();
        $columnTypes = $this->getColumnTypes();

        // We can't add rows if there are no columns
        if ($columnCount == 0) {
            throw new UndefinedColumnsException;
        }

        // If the value is null, create a nulled Row
        if (is_null($values)) {
            return $this->pushRow(
                Row::createNull($columnCount)
            );
        }

        // If the value is already a Row object, add it.
        if ($values instanceof Row) {
            $this->verifyRowCellCount($values);

            return $this->pushRow($values);
        }

        // If it is neither of the above, and not an array, fail.
        if (! is_array($values)) {
            throw new InvalidArgumentException($values, 'array or null');
        }

        // If it is an array, but the cell to column count doesn't match, fail.
        if (count($values) > $columnCount) {
            throw new InvalidCellCount($columnCount);
        }

        // Finally, parse the array of cells into a Row so it can be added.
        foreach ($values as $index => $cellValue) {
            // Null is null, don't do anything else.
            if (is_null($cellValue)) {
                $newRow->addCell(
                    new Cell(null)
                );
            } else {
                // If the cellValue is part of a date / time column, process accordingly
                if (in_array($columnTypes[$index], self::DATE_TIME_COLUMNS)) {
                    $newRow->addCell(
                        $this->createDateCell($cellValue)
                    );
                } else {
                    // If the cellValue is an array explicitly defining a Cell,
                    // or a null, then create a new Cell with the values.
                    if (is_array($cellValue)) {
                        $newRow->addCell(
                            Cell::createFromArray($cellValue)
                        );
                    }

                    // If not caught by anything else, then just create a cell with the value
                    $newRow->addCell(
                        new Cell($cellValue)
                    );
                }
            }
        }

        $this->verifyRowCellCount($newRow);

        return $this->pushRow($newRow);
    }

    /**
     * Adds multiple rows to the DataTable.
     *
     * @see    addRow()
     * @param  Row[] $arrayOfRows
     * @return self
     * @throws \Khill\Lavacharts\Exceptions\InvalidRowDefinition
     */
    public function addRows($arrayOfRows)
    {
        if (method_exists($arrayOfRows, 'toArray')) {
            $arrayOfRows = $arrayOfRows->toArray();
        }

        array_walk($arrayOfRows, [$this, 'addRow']);

        return $this;
    }

    /**
     * Return a Cell from the specified row and column index.
     *
     * @since 4.0.0
     * @param int $rowIndex
     * @param int $columnIndex
     * @return Cell
     */
    public function getCell($rowIndex, $columnIndex)
    {
        return $this->getRow($rowIndex)->getCell($columnIndex);
    }

    /**
     * Returns the Row at the given index.
     *
     * @since 4.0.0
     * @param int $index
     * @return Row
     */
    public function getRow($index)
    {
        return $this->rows[$index];
    }

    /**
     * Returns the rows array from the DataTable
     *
     * @return Row[]
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
     * @return Column
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnIndex
     */
    public function getColumn($index)
    {
        $this->indexCheck($index);

        return $this->columns[$index];
    }

    /**
     * Returns the column array from the DataTable
     *
     * @return Column[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Returns the number of columns in the DataTable
     *
     * @return int
     */
    public function getColumnCount()
    {
        return count($this->columns);
    }

    /**
     * Create a new Filter by name and index / label.
     *
     * @since 4.0.0
     * @param string     $filterType
     * @param int|string $indexOrLabel
     * @param array      $options
     * @return Filter
     */
    public function getColumnFilter($filterType, $indexOrLabel, array $options = [])
    {
        return new Filter($filterType, $indexOrLabel, $options);
    }

    /**
     * Returns the columns who's type matches the given value.
     *
     * @since  3.0.0
     * @param  string $type
     * @return array
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnType
     */
    public function getColumnsByType($type)
    {
        Column::isValidType($type);

        return array_filter(
            $this->columns,
            function (Column $column) use ($type) {
                return $column->getType() == $type;
            }
        );
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
        return array_map(function (Column $column) {
            return $column->getType();
        }, $this->columns);
    }

    /**
     * Returns the labels of columns currently defined.
     *
     * @since  3.0.0
     * @return string[]
     */
    public function getColumnLabels()
    {
        return array_map(function (Column $column) {
            return $column->getLabel();
        }, $this->columns);
    }

    /**
     * Returns the formatted columns in an array from the DataTable
     *
     * @since  3.0.0
     * @return Column[]
     */
    public function getFormattedColumns()
    {
        $formattedColumns = [];

        for ($index = 0; $index < count($this->columns); $index++) {
            if ($this->columns[$index]->isFormatted()) {
                $this->columns[$index]->getFormat()->setIndex($index);

                $formattedColumns[$index] = $this->columns[$index];
            }
        }

        return $formattedColumns;
    }

    /**
     * Check if any of the defined Columns are formatted.
     *
     * @return bool
     */
    public function hasFormattedColumns()
    {
        return count($this->getFormattedColumns()) > 0;
    }

    /**
     * Sets a Column at a specified index.
     *
     * @since  4.0.0
     * @param  int    $index
     * @param  Column $column
     * @return self
     */
    public function setColumn($index, Column $column)
    {
        $this->indexCheck($index);

        $this->columns[$index] = $column;

        return $this;
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
        if (is_int($index) === false || isset($this->columns[$index]) === false) {
            throw new InvalidColumnIndex($index, count($this->columns));
        }
    }

    /**
     * Push a Column onto the DataTable
     *
     * @access private
     * @since  4.0.0
     * @param  Column $column
     * @return self
     */
    protected function pushColumn(Column $column)
    {
        $this->columns[] = $column;

        return $this;
    }

    /**
     * Push a Row onto the DataTable
     *
     * @access private
     * @since  4.0.0
     * @param  Row $row
     * @return self
     */
    protected function pushRow(Row $row)
    {
        $this->rows[] = $row;

        return $this;
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
     * @access protected
     * @param string $tz
     * @return bool
     */
    protected function isValidTimeZone($tz)
    {
        $timezoneList = call_user_func_array('array_merge', timezone_abbreviations_list());

        $timezones = array_map(
            function ($timezone) {
                if ($timezone['timezone_id'] != null) {
                    return strtolower($timezone['timezone_id']);
                }
            },
            $timezoneList
        );

        $timezones = array_filter($timezones, 'is_string');
        $timezones = array_unique($timezones);

        return in_array(strtolower($tz), $timezones, true);
    }

    /**
     * Checks to see if a Row has the correct number of Cells for the DataTable
     *
     * @since 4.0.0
     * @param Row $row
     * @throws \Khill\Lavacharts\Exceptions\InvalidCellCount
     */
    private function verifyRowCellCount(Row $row)
    {
        $cellCount = $row->getCellCount();

        if ($cellCount > $this->getColumnCount()) {
            throw new InvalidCellCount($cellCount);
        }
    }

    /**
     * Create a new DateCell from the given value.
     *
     * @param $cellValue
     * @return Cell
     * @throws InvalidArgumentException
     */
    private function createDateCell($cellValue)
    {
        $dateTimeFormat = $this->getOptions()->get('datetime_format');

        // DateCells can only be created by Carbon instances or
        // strings consumable by Carbon so....
        if ($cellValue instanceof Carbon === false && is_string($cellValue) === false) {
            throw new InvalidArgumentException($cellValue, 'string or Carbon object');
        }

        // If the cellValue is already a Carbon instance,
        // create a new DateCell from it.
//        if ($cellValue instanceof Carbon) {
//            return DateCell::create($cellValue);
//        }

        // If no format string was defined in the options, then
        // attempt to implicitly parse the string. If the format
        // string is not empty, then attempt to use it to explicitly
        // create a Carbon instance from the format.
        if (! empty($dateTimeFormat)) {
            return DateCell::createFromFormat($dateTimeFormat, $cellValue);
        } else {
            return DateCell::create($cellValue);
        }
    }
}
