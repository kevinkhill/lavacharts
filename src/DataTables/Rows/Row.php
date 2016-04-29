<?php

namespace Khill\Lavacharts\DataTables\Rows;

use Carbon\Carbon;
use Khill\Lavacharts\DataTables\Cells\DateCell;
use Khill\Lavacharts\DataTables\DataTable;
use Khill\Lavacharts\Exceptions\InvalidColumnIndex;

/**
 * Row Object
 *
 * The row object contains all the data for a row, stored in an array, indexed by columns.
 *
 *
 * @package   Khill\Lavacharts\DataTables\Rows
 * @since     3.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2016, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class Row implements \JsonSerializable
{
    /**
     * Row values
     *
     * @var Cell[]
     */
    protected $values;

    /**
     * Creates a new Row object with the given values.
     *
     * @param array $valueArray Array of row values.
     */
    public function __construct($valueArray)
    {
        $this->values = array_map(function ($cellValue) {
            if ($cellValue instanceof Carbon) {
                return new DateCell($cellValue);
            } else {
                return $cellValue;
            }
        }, $valueArray);
    }

    /**
     * Creates a new Row object from an array of values.
     *
     * @param \Khill\Lavacharts\DataTables\DataTable $datatable
     * @param  array                                 $valueArray Array of values to assign to the row.
     * @return \Khill\Lavacharts\DataTables\Rows\Row
     * @throws \Khill\Lavacharts\DataTables\Rows\InvalidCellCount
     * @throws \Khill\Lavacharts\DataTables\Rows\InvalidRowDefinition
     */
    public static function create(DataTable $datatable, $valueArray)
    {
        if ($valueArray !== null && is_array($valueArray) === false) {
            throw new InvalidRowDefinition($valueArray);
        }

        if ($valueArray === null || is_array($valueArray) && empty($valueArray)) {
            return new NullRow($datatable->getColumnCount());
        }

        $cellCount   = count($valueArray);
        $columnCount = $datatable->getColumnCount();

        if ($cellCount > $columnCount) {
            throw new InvalidCellCount($cellCount, $columnCount);
        }

        $columnTypes    = $datatable->getColumnTypes();
        $dateTimeFormat = $datatable->getDateTimeFormat();

        $rowData = [];

        foreach ($valueArray as $index => $cell) {
            if ((bool) preg_match('/date|datetime|timeofday/', $columnTypes[$index]) === true) {
                if ($cell instanceof Carbon) {
                    $rowData[] = new DateCell($cell);
                } else {
                    if (isset($dateTimeFormat)) {
                        $rowData[] = DateCell::parseString($cell, $dateTimeFormat);
                    } else {
                        $rowData[] = DateCell::parseString($cell);
                    }
                }
            } else {
                $rowData[] = $cell;
            }
        }

        return new self($rowData);
    }

    /**
     * Returns a column value from the Row.
     *
     * @access public
     * @param  int $columnIndex Column value to fetch from the row.
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnIndex
     * @return mixed
     */
    public function getColumnValue($columnIndex)
    {
        if (is_int($columnIndex) === false || isset($this->values[$columnIndex]) === false) {
            throw new InvalidColumnIndex($columnIndex, count($this->values));
        }

        return $this->values[$columnIndex];
    }

    /**
     * Custom json serialization of the row.
     *
     * @access public
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'c' => array_map(function ($cellValue) {
                return [ 'v' => $cellValue ];
            }, $this->values)
        ];
    }
}
