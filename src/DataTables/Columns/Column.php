<?php

namespace Khill\Lavacharts\DataTables\Columns;

use Khill\Lavacharts\Exceptions\InvalidColumnType;
use Khill\Lavacharts\Support\Contracts\Arrayable;
use Khill\Lavacharts\Support\Contracts\Jsonable;
use Khill\Lavacharts\Support\Contracts\Customizable;
use Khill\Lavacharts\Support\Traits\ArrayToJsonTrait as ArrayToJson;
use Khill\Lavacharts\Support\Traits\HasOptionsTrait as HasOptions;
use Khill\Lavacharts\Support\StringValue;

/**
 * Column Object
 *
 * The Column object is used to define the different columns for a DataTable.
 *
 *
 * @package       Khill\Lavacharts\DataTables\Columns
 * @since         3.0.0
 * @author        Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link          http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link          http://lavacharts.com                   Official Docs Site
 * @license       http://opensource.org/licenses/MIT      MIT
 */
class Column implements Customizable, Arrayable, Jsonable
{
    use HasOptions, ArrayToJson;

    /**
     * Valid column types
     *
     * @var array
     */
    const TYPES = [
        'role',
        'string',
        'number',
        'boolean',
        'date',
        'datetime',
        'timeofday',
    ];

    /**
     * Column type.
     *
     * @var string
     */
    protected $type;

    /**
     * Column label.
     *
     * @var string
     */
    protected $label;

    /**
     * Column formatter.
     *
     * @var Format
     */
    protected $format;

    /**
     * Column role.
     *
     * @var string
     */
    protected $role;

    /**
     * Creates a new Column with the defined label.
     *
     * @param  string $type    Column type
     * @param  string $label   Column label (optional)
     * @param  Format $format  Column format(optional)
     * @param  Role   $role    Column role (optional)
     * @param  array  $options Column options (optional)
     */
    public function __construct(
        $type,
        $label = '',
        Format $format = null,
        Role $role = null,
        array $options = []
    )
    {
        $this->setOptions($options);

        $this->type   = $type;
        $this->label  = $label;
        $this->format = $format;
        $this->role   = $role;
    }

    /**
     * Get a new instance of the ColumnBuilder
     *
     * @return ColumnBuilder
     */
    public static function createBuilder()
    {
        return new ColumnBuilder;
    }

    /**
     * Checks if a given type is a valid column type
     *
     * @param  string $type
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnType
     */
    public static function isValidType($type)
    {
        if (! in_array($type, self::TYPES, true)) {
            throw new InvalidColumnType($type);
        }
    }

    /**
     * Returns the type of column.
     *
     * @return string Column type.
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the column label.
     *
     * @return string Column label.
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Returns the column formatter.
     *
     * @return Format
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Sets the formatter for the column.
     *
     * @param Format $format
     */
    public function setFormat(Format $format)
    {
        $this->format = $format;
    }

    /**
     * Returns the status of if the column is formatted.
     *
     * @return boolean
     */
    public function isFormatted()
    {
        return ($this->format instanceof Format);
    }

    /**
     * Returns the column role.
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Return the Column as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $column = [
            'type' => $this->type,
        ];

        if (StringValue::isNonEmpty($this->label)) {
            $column['label'] = $this->label;
        }

        if ($this->role instanceof Role) {
            $column['p'] = ['role' => (string) $this->role];

            if ($this->hasOptions()) {
                $column['p'] = array_merge($column['p'], $this->options);
            }
        }

        return $column;
    }
}
