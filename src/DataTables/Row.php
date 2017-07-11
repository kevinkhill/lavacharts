<?php

namespace Khill\Lavacharts\DataTables;

use ArrayIterator;
use Carbon\Carbon;
use Countable;
use IteratorAggregate;
use Khill\Lavacharts\DataTables\Cells\Cell;
use Khill\Lavacharts\DataTables\Cells\DateCell;
use Khill\Lavacharts\Exceptions\InvalidArgumentException;
use Khill\Lavacharts\Exceptions\InvalidColumnIndex;
use Khill\Lavacharts\Support\Contracts\Arrayable;
use Khill\Lavacharts\Support\Contracts\ArrayAccess;
use Khill\Lavacharts\Support\Contracts\Jsonable;
use Khill\Lavacharts\Support\Traits\ArrayAccessTrait as DynamicArrayAccess;
use Khill\Lavacharts\Support\Traits\ArrayToJsonTrait as ArrayToJson;

/**
 * Row Object
 *
 * The row object contains all the data for a row, stored in an array, indexed by columns.
 *
 *
 * @package       Khill\Lavacharts\DataTables\Rows
 * @since         3.0.0
 * @author        Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link          http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link          http://lavacharts.com                   Official Docs Site
 * @license       http://opensource.org/licenses/MIT      MIT
 */
class Row implements Arrayable, ArrayAccess, Countable, IteratorAggregate, Jsonable
{
    use DynamicArrayAccess, ArrayToJson;

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
     *
     * @param int $columnCount
     *
     * @return Row
     * @throws InvalidArgumentException
     */
    public static function createNull($columnCount)
    {
        if (! is_int($columnCount)) {
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

    public function getArrayAccessProperty()
    {
        return 'cells';
    }

    /**
     * Get an iterator for the renderables.
     *
     * @since 3.2.0 Enable the use of foreach on the Row to access the cells
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->cells);
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
     *
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
}
