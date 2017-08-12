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
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
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
     * @throws \Khill\Lavacharts\Exceptions\UndefinedDateCellException
     */
    public static function create($datetime)
    {
        try {
            $carbon = new Carbon(Str::verify($datetime));

            return new static($carbon);
        } catch (Exception $e) {
            throw new UndefinedDateCellException($datetime);
        }
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
        try {
            $carbon = Carbon::createFromFormat(Str::verify($format), Str::verify($datetime));

            return new static($carbon);
        } catch (Exception $e) {
            throw new UndefinedDateCellException($datetime, $format);
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
    public function getJavascriptFormat()
    {
        return 'Date(%d,%d,%d,%d,%d,%d)';
    }

    /**
     * @inheritdoc
     */
    public function getJavascriptSource()
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
