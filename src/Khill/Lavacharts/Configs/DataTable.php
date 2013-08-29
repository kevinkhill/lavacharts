<?php namespace Khill\Lavacharts\Configs;
/**
 * DataTable Object
 *
 * The DataTable object is used to hold the data passed into a visualization.
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
 * @author Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2013, KHill Designs
 * @link https://github.com/kevinkhill/Codeigniter-gCharts GitHub Repository Page
 * @link http://kevinkhill.github.io/Codeigniter-gCharts/ GitHub Project Page
 * @license http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Helpers\Helpers;

class DataTable
{
    /**
     * Holds the information defining the columns.
     *
     * @var array
     */
    public $cols = array();

    /**
     * Holds the information defining each row.
     *
     * @var array
     */
    public $rows = array();

    //@TODO: private $_row_count;


    /**
     * Adds a column to the DataTable
     *
     * First signature has the following parameters:
     * type - A string with the data type of the values of the column.
     * The type can be one of the following: 'string' 'number' 'boolean' 'date'
     * 'datetime' 'timeofday'.
     *
     * opt_label - [Optional] A string with the label of the column. The column
     * label is typically displayed as part of the visualization, for example as
     *  a column header in a table, or as a legend label in a pie chart. If not
     * value is specified, an empty string is assigned.
     * opt_id - [Optional] A string with a unique identifier for the column. If
     * not value is specified, an empty string is assigned.
     *
     *
     * @param string Describing the column data type
     * @param string A label for the column. (Optional)
     * @param string An ID for the column. (Optinal)
     * @return \DataTable
     */
    public function addColumn($typeOrDescriptionArray, $opt_label = '', $opt_id = '')
    {
        $types = array(
            'string',
            'number',
            'boolean',
            'date',
            'datetime',
            'timeofday'
        );

        $descriptions = array(
            'type',
            'label',
            'id',
            'role',
            'pattern'
        );

        switch(gettype($typeOrDescriptionArray))
        {
            case 'array':
                foreach($typeOrDescriptionArray as $key => $value)
                {
                    if(array_key_exists('type', $typeOrDescriptionArray))
                    {
                        if(in_array($typeOrDescriptionArray['type'], $types))
                        {
                            $descArray['type'] = $typeOrDescriptionArray['type'];

                            if(in_array($key, $descriptions))
                            {
                                if($key != 'type')
                                {
                                    if(is_string($value))
                                    {
                                        $descArray[$key] = $value;
                                    } else {
                                        $this->error('Invalid description array value, must be type (string).');
                                    }
                                }
                            } else {
                                $this->error('Invalid description array key value, must be type (string) with any key value '.Helpers::array_string($descriptions));
                            }
                        } else {
                            $this->error('Invalid type, must be type (string) with the value '.Helpers::array_string($types));
                        }
                    } else {
                        $this->error('Invalid description array, must contain (array) with at least one key type (string) value [ type ]');
                    }
                }

                $this->cols[] = $descArray;
            break;

            case 'string':
                if(in_array($typeOrDescriptionArray, $types))
                {
                    $descArray['type'] = $typeOrDescriptionArray;

                    if(is_string($opt_label))
                    {
                        $descArray['label'] = $opt_label;
                    } else {
                        $this->error('Invalid opt_label, must be type (string).');
                    }

                    if(is_string($opt_id))
                    {
                        $descArray['id'] = $opt_id;
                    } else {
                        $this->error('Invalid opt_id, must be type (string).');
                    }
                } else {
                    $this->error('Invalid type, must be type (string) with the value '.Helpers::array_string($types));
                }

                $this->cols[] = $descArray;
            break;

            default:
                $this->error('Invalid type or description array, must be type (string) or (array).');
            break;
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
     * in cols. To indicate a null cell, you can specify null, leave a blank for
     * a cell in an array, or omit trailing array members. So, to indicate a row
     * with null for the first two cells, you would specify [null, null, {cell_val}].
     *
     * @see \DataCell
     * @param mixed $opt_cell Array of values or DataCells.
     * @return \DataTable
     */
    public function addRow($opt_cellArray = NULL)
    {
        $props = array(
            'v',
            'f',
            'p'
        );

        if(is_null($opt_cellArray))
        {
            for($a = 0; $a < count($this->cols); $a++)
            {
                $tmp[] = array('v' => NULL);
            }
            $this->rows[] = array('c' => $tmp);
        } else {
            if(is_array($opt_cellArray))
            {
                if(Helpers::array_is_multi($opt_cellArray))
                {
                    foreach($opt_cellArray as $prop => $value)
                    {
                        if(in_array($prop, $props))
                        {
                            $rowVals[] = array($prop => $value);
                        } else {
                            $this->error('Invalid row property, array with keys type (string) with values [ v | f | p ] ');
                        }
                    }

                    $this->rows[] = array('c' => $rowVals);
                } else {
                    if(count($opt_cellArray) <= count($this->cols))
                    {
                        for($b = 0; $b < count($this->cols); $b++)
                        {
                            if(isset($opt_cellArray[$b]))
                            {
                                if(Helpers::is_jsDate($opt_cellArray[$b]))
                                {
                                    $rowVals[] = array('v' => $opt_cellArray[$b]->toString());
                                } else {
                                    $rowVals[] = array('v' => $opt_cellArray[$b]);
                                }
                            } else {
                                $rowVals[] = array('v' => NULL);
                            }
                        }
                        $this->rows[] = array('c' => $rowVals);
                    } else {
                        $msg = 'Invalid number of cells, must be equal or less than number of columns. ';
                        $msg .= '(cells '.count($opt_cellArray).' > cols '.count($this->cols).')';
                        $this->error($msg);
                    }
                }
            } else {
                $this->error('Invalid row definition, must be type (array)');
            }
        }

        return $this;
    }

    /**
     * Adds multiple rows to the DataTable.
     *
     * @see addRow()
     * @param array Multi-dimensional array of rows.
     * @return \DataTable
     */
    public function addRows($arrayOfRows)
    {
        if(Helpers::array_is_multi($arrayOfRows))
        {
            foreach($arrayOfRows as $row)
            {
                $this->addRow($row);
            }
        } else {
            $this->error('Invalid value for addRows, must be type (array), multi-dimensional.');
        }

        return $this;
    }
/*
    public function getColumnId($columnIndex)
    {

    }

    public function getColumnLabel($columnIndex)
    {

    }

    public function getColumnPattern($columnIndex)
    {

    }

    public function getColumnProperty($columnIndex, $name)
    {

    }

    public function getColumnRange($columnIndex)
    {

    }

    public function getColumnRole($columnIndex)
    {

    }

    public function getColumnType($columnIndex)
    {

    }

    public function getDistinctValues($columnIndex)
    {

    }

    public function getFilteredRows($filters)
    {

    }

    public function getFormattedValue($rowIndex, $columnIndex)
    {

    }

    public function getNumberOfColumns()
    {
        return count($this->cols);
    }

    public function getNumberOfRows()
    {
        return count($this->rows);
    }

    public function getProperties($rowIndex, $columnIndex)
    {

    }

    public function getProperty($rowIndex, $columnIndex, $name)
    {

    }

    public function getRowProperties($rowIndex)
    {

    }

    public function getRowProperty($rowIndex, $name)
    {

    }

    public function getSortedRows($sortColumns)
    {

    }

    public function getTableProperties()
    {

    }

    public function getTableProperty($name)
    {

    }

    public function getValue($rowIndex, $columnIndex)
    {

    }

    public function insertColumn($columnIndex, $type, $label='', $id='')
    {

    }

    public function insertRows($rowIndex, $numberOrArray)
    {

    }

    public function removeColumn($columnIndex)
    {

    }

    public function removeColumns($columnIndex, $numberOfColumns)
    {

    }

    public function removeRow($rowIndex)
    {

    }

    public function removeRows($rowIndex, $numberOfRows)
    {

    }

    public function setCell($rowIndex, $columnIndex, $value='', $formattedValue='', $properties='')
    {

    }

    public function setColumnLabel($columnIndex, $label)
    {

    }

    public function setColumnProperty($columnIndex, $name, $value)
    {

    }

    public function setColumnProperties($columnIndex, $properties)
    {

    }

    public function setFormattedValue($rowIndex, $columnIndex, $formattedValue)
    {

    }

    public function setProperty($rowIndex, $columnIndex, $name, $value)
    {

    }

    public function setProperties($rowIndex, $columnIndex, $properties)
    {

    }

    public function setRowProperty($rowIndex, $name, $value)
    {

    }

    public function setRowProperties($rowIndex, $properties)
    {

    }

    public function setTableProperty($name, $value)
    {

    }

    public function setTableProperties($properties)
    {

    }

    public function setValue($rowIndex, $columnIndex, $value)
    {

    }

    public function sort($sortColumns)
    {

    }
*/
    public function toJSON()
    {
        return json_encode($this);
    }

    /**
     * Adds the error message to the error log in the lavacharts master object.
     *
     * @param string $msg error message.
     */
    private function error($msg)
    {
        Lavacharts::_set_error(get_class($this), $msg);
    }

}
