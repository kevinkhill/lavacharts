<?php

namespace Khill\Lavacharts\DataTables\Rows;

use \Carbon\Carbon;
use \Khill\Lavacharts\Exceptions\InvalidColumnIndex;

/**
 * Row Object
 *
 * The row object contains all the data for a row, stored in an array, indexed by columns.
 *
 *
 * @package    Khill\Lavacharts\DataTables\Rows
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class Row implements \JsonSerializable
{
    /**
     * Row values
     *
     * @var array
     */
    private $values;

    /**
     * Creates a new Row object with the given values.
     *
     * @param  array $valueArray Array of row values.
     * @return self
     */
    public function __construct($valueArray)
    {
        $this->values = $valueArray;
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
        try {
            return $this->values[$columnIndex];
        } catch (\Exception $e) {
            throw new InvalidColumnIndex($columnIndex, count($this->values));
        }
    }

    /**
     * Custom json serialization of the column.
     *
     * @access public
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'c' => array_map(function ($cellValue) {
                if ($cellValue instanceof Carbon) {
                    return ['v' => $this->carbonToJsString($cellValue)];
                } else {
                    return ['v' => $cellValue];
                }
            }, $this->values)
        ];
    }

    /**
     * Outputs the Carbon object as a valid javascript Date string.
     *
     * @access protected
     * @param  \Carbon\Carbon $carbon A Carbon instance
     * @return string Javscript date declaration
     */
    protected function carbonToJsString(Carbon $carbon)
    {
        return sprintf(
            'Date(%d,%d,%d,%d,%d,%d)',
            isset($carbon->year)   ? $carbon->year      : 'null',
            isset($carbon->month)  ? $carbon->month - 1 : 'null', //silly javascript
            isset($carbon->day)    ? $carbon->day       : 'null',
            isset($carbon->hour)   ? $carbon->hour      : 'null',
            isset($carbon->minute) ? $carbon->minute    : 'null',
            isset($carbon->second) ? $carbon->second    : 'null'
        );
    }
}
