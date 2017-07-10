<?php

namespace Khill\Lavacharts\DataTables\Cells;

use Carbon\Carbon;
use Exception;
use Khill\Lavacharts\Exceptions\UndefinedDateCellException;

/**
 * DateCell Class
 *
 * Wrapper object to implement JsonSerializable on the Carbon object.
 *
 * @package   Khill\Lavacharts\DataTables\Cells
 * @since     3.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 * @property  Carbon $v
 */
class DateCell extends Cell
{
    /**
     * Parses a datetime string with or without a datetime format.
     *
     * Uses Carbon to create the values for the DateCell.
     *
     * @param  string $datetime
     * @return Cell
     * @throws \Khill\Lavacharts\Exceptions\UndefinedDateCellException
     */
    public static function create($datetime)
    {
        try {
            $carbon = new Carbon($datetime);

            return new self($carbon);
        } catch (Exception $e) {
            throw new UndefinedDateCellException($datetime);
        }
    }

    /**
     * Creates a new DateCell object from a Carbon object.
     *
     * @param  \Carbon\Carbon $carbon
     * @param  string         $format
     * @param  array          $options
     */
    public function __construct(Carbon $carbon, $format = '', array $options = [])
    {
        parent::__construct($carbon, $format, $options);
    }

    /**
     * Create a new DateCell from a datetime string and a format.
     *
     * Carbon is used to validate the input.
     *
     * @param  string $format
     * @param  string $datetime
     * @return DateCell
     * @throws \Khill\Lavacharts\Exceptions\UndefinedDateCellException
     */
    public static function createFromFormat($format, $datetime)
    {
//        try { TODO: why?
            $carbon = Carbon::createFromFormat($format, $datetime);

            return new static($carbon);
//        } catch (Exception $e) {
//            throw new UndefinedDateCellException($datetime, $format);
//        }
    }

    /**
     * Custom string output of the Carbon date.
     *
     * @return string
     */
    public function __toString()
    {
        if ($this->v === null) {
            return 'null';
        }

        return sprintf(
            'Date(%d,%d,%d,%d,%d,%d)',
            isset($this->v->year)   ? $this->v->year      : 'null',
            isset($this->v->month)  ? $this->v->month - 1 : 'null', //silly javascript
            isset($this->v->day)    ? $this->v->day       : 'null',
            isset($this->v->hour)   ? $this->v->hour      : 'null',
            isset($this->v->minute) ? $this->v->minute    : 'null',
            isset($this->v->second) ? $this->v->second    : 'null'
        );
    }

    /**
     * Return the DateCell as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return ['v' => $this->__toString()];
    }
}
