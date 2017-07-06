<?php

namespace Khill\Lavacharts\DataTables;

use ArrayAccess;
use Carbon\Carbon;
use Countable;
use Khill\Lavacharts\DataTables\Cells\Cell;
use Khill\Lavacharts\DataTables\Cells\DateCell;
use Khill\Lavacharts\Exceptions\InvalidArgumentException;
use Khill\Lavacharts\Exceptions\InvalidColumnIndex;
use Khill\Lavacharts\Support\Contracts\Arrayable;
use Khill\Lavacharts\Support\Contracts\Jsonable;
use Khill\Lavacharts\Support\Contracts\DataInterface;
use Khill\Lavacharts\Support\Traits\ArrayToJsonTrait as ArrayToJson;

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
     * Create a new row filled with null values
     *
     * @since 3.2.0
     * @param int $columnCount
     * @return Row
     */
    public static function createNull($columnCount)
    {
        if (!is_int($columnCount)) {
            throw new InvalidArgumentException($columnCount, 'integer');
        }

        return new self(array_fill(0, $columnCount, null));
    }

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
