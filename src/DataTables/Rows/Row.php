<?php

namespace Khill\Lavacharts\DataTables\Rows;

use ArrayAccess;
use Carbon\Carbon;
use Countable;
use IteratorAggregate;
use Khill\Lavacharts\DataTables\Cells\Cell;
use Khill\Lavacharts\DataTables\Cells\DateCell;
use Khill\Lavacharts\Exceptions\InvalidArgumentException;
use Khill\Lavacharts\Exceptions\InvalidColumnIndex;
use Khill\Lavacharts\Support\Contracts\Arrayable;
use Khill\Lavacharts\Support\Contracts\Jsonable;
use Khill\Lavacharts\Support\Contracts\DataInterface;
use Khill\Lavacharts\Support\Traits\ArrayToJsonTrait as ArrayToJson;
use Traversable;

/**
 * Row Object
 *
 * The row object contains all the data for a row, stored in an array, indexed by columns.
 *
 *
 * @package   Khill\Lavacharts\DataTables\Rows
 * @since     3.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class Row implements Arrayable, ArrayAccess, Countable, Jsonable
{
    use ArrayToJson;

    /**
     * Row values
     *
     * @var Cell[]
     */
    protected $cells = [];

    /**
     * Creates a new Row object with the given values from an array.
     *
     * While iterating through the array, if the value is a...
     *  - Cell, pass it through
     *  - Scalar value, create a Cell
     *  - Carbon instance, create a DateCell
     *
     * @param array $values Array of row values.
     */
    public function __construct(array $values = [])
    {
        foreach ($values as $cellValue) {
            if ($cellValue instanceof Cell) {
                $this->cells[] = $cellValue;
            }

            if ($cellValue instanceof Carbon) {
                $this->cells[] = new DateCell($cellValue);
            }

            $this->cells[] = new Cell($cellValue);
        }
    }

    /**
     * Creates a new Row object from an array of values.
     *
     * @param  DataInterface $data
     * @param  array         $values Array of values to assign to the row.
     * @return \Khill\Lavacharts\DataTables\Rows\Row
     * @throws \Khill\Lavacharts\Exceptions\UndefinedDateCellException
     * @throws \Khill\Lavacharts\Exceptions\InvalidDateTimeString
     */
    public static function createFor(DataInterface $data, $values)
    {
        $datatable      = $data->getDataTable();
        $columnTypes    = $datatable->getColumnTypes();
        $dateTimeFormat = $datatable->getOptions()->get('datetime_format');

        $rowData = [];

        foreach ($values as $index => $cellValue) {
            // Regardless of column type, if a Cell is explicitly defined by
            // an array, then create a new Cell with the values.
            if (is_array($cellValue)) {
                $rowData[] = Cell::create($cellValue);
            }

            // Logic for handling datetime related cells
            if (preg_match('/date|time/', $columnTypes[$index])) {

                // DateCells can only be created by Carbon instances or
                // strings consumable by Carbon so....
                if ($cellValue instanceof Carbon === false && is_string($cellValue) === false) {
                    throw new InvalidArgumentException($cellValue, 'string or Carbon object');
                }

                // If the cellValue is already a Carbon instance,
                // create a new DateCell from it.
                if ($cellValue instanceof Carbon) {
                    $rowData[] = new DateCell($cellValue);
                }

                // If no format string was defined in the options, then
                // attempt to implicitly parse the string. If the format
                // string is not empty, then attempt to use it to explicitly
                // create a Carbon instance from the format.
                if (empty($dateTimeFormat)) {
                    $rowData[] = DateCell::create($cellValue);
                } else {
                    $rowData[] = DateCell::createFromFormat($cellValue, $dateTimeFormat);
                }
            }

            // Pass through any non-explicit & non-datetime values
            $rowData[] = $cellValue;
        }

        return new self($rowData);
    }

    /**
     * Returns the cells from the Row.
     *
     * @since 3.2.0
     * @return Cell[]
     */
    public function getCells()
    {
        return $this->cells;
    }

    /**
     * Returns the Cell at the given index from the Row.
     *
     * @param  int $index Column index to fetch from the row.
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnIndex
     * @return Cell
     */
    public function getCell($index)
    {
        if (is_int($index) === false || isset($this->cells[$index]) === false) {
            throw new InvalidColumnIndex($index, count($this->cells));
        }

        return $this->cells[$index];
    }

    /**
     * Returns the Row as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return ['c' => $this->cells];
    }

    /**
     * Returns the number of cells
     *
     * @return int
     */
    public function count()
    {
        return count($this->cells);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->cells[] = $value;
        } else {
            $this->cells[$offset] = $value;
        }
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->cells[$offset]);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->cells[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return isset($this->cells[$offset]) ? $this->cells[$offset] : null;
    }
}
