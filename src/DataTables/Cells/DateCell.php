<?php

namespace Khill\Lavacharts\DataTables\Cells;

use Carbon\Carbon;
use Exception;
use Khill\Lavacharts\Exceptions\UndefinedDateCellException;
use Khill\Lavacharts\Support\Contracts\Javascriptable;
use Khill\Lavacharts\Support\StringValue as Str;
use Khill\Lavacharts\Support\Traits\ToJavascriptTrait as ToJavascript;

/**
 * DateCell Class
 *
 * Wrapper object to implement JsonSerializable on the Carbon object.
 *
 * @package   Khill\Lavacharts\DataTables\Cells
 * @since     3.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright 2020 Kevin Hill
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 * @property  Carbon $v
 */
class DateCell extends Cell implements Javascriptable
{
    use ToJavascript;

    /**
     * Parses a datetime string with or without a datetime format.
     *
     * Uses Carbon to create the values for the DateCell.
     *
     * @param  string $datetime
     * @return Cell
     */
    public static function create($datetime)
    {
        $carbon = new Carbon(Str::verify($datetime));

        return new static($carbon);
    }

    /**
     * Create a new DateCell from a datetime string and a format.
     *
     * Carbon is used to validate the input.
     *
     * @param  string $format
     * @param  string $datetime
     * @return DateCell
     */
    public static function createFromFormat($format, $datetime)
    {
        $carbon = Carbon::createFromFormat(Str::verify($format), Str::verify($datetime));

        return new static($carbon);
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
     * Return the DateCell as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $this->value = $this->toJavascript();

        return parent::toArray();
    }

    /**
     * @inheritdoc
     */
    public function toJavascript()
    {
        return vsprintf('Date(%d,%d,%d,%d,%d,%d)', $this->getDateValues());
    }

    /**
     * Returns an array with nulls for missing pieces for the DateCell
     *
     * @return array
     */
    private function getDateValues()
    {
        return [
            isset($this->value->year)   ? $this->value->year      : 'null',
            isset($this->value->month)  ? $this->value->month - 1 : 'null', //silly javascript
            isset($this->value->day)    ? $this->value->day       : 'null',
            isset($this->value->hour)   ? $this->value->hour      : 'null',
            isset($this->value->minute) ? $this->value->minute    : 'null',
            isset($this->value->second) ? $this->value->second    : 'null'
        ];
    }
}
