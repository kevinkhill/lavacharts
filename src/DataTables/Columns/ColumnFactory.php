<?php

namespace Khill\Lavacharts\DataTables\Columns;

use Khill\Lavacharts\Values\StringValue;
use Khill\Lavacharts\DataTables\Formats\Format;
use Khill\Lavacharts\Exceptions\InvalidColumnRole;
use Khill\Lavacharts\Exceptions\InvalidColumnType;
use Khill\Lavacharts\Exceptions\InvalidStringValue;

/**
 * ColumnFactory Class
 *
 * The ColumnFactory creates new columns for DataTables. The only mandatory parameter is
 * the type of column to create, all others are optional.
 *
 *
 * @package   Khill\Lavacharts\DataTables\Columns
 * @since     3.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2016, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class ColumnFactory
{
    /**
     * Valid column types
     *
     * @var array
     */
    public static $types = [
        'role',
        'string',
        'number',
        'boolean',
        'date',
        'datetime',
        'timeofday'
    ];

    /**
     * Valid column roles
     *
     * @var array
     */
    public static $roles = [
        'annotation',
        'annotationText',
        'certainty',
        'emphasis',
        'interval',
        'scope',
        'style',
        'tooltip'
    ];

    /**
     * Valid column descriptions
     *
     * @var array
     */
    public static $desc = [
        'type',
        'label',
        'id',
        'role',
        'pattern'
    ];

    /**
     * Creates a new column object.
     *
     * @access public
     * @since  3.0.0
     * @param  string                                      $type Type of column to create.
     * @param  string                                      $label A label for the column.
     * @param  \Khill\Lavacharts\DataTables\Formats\Format $format Column formatter for the data.
     * @param  string                                      $role A role for the column to play.
     * @return \Khill\Lavacharts\DataTables\Columns\Column
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnRole
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnType
     */
    public function create($type, $label = '', Format $format = null, $role = '')
    {
        if (in_array($type, self::$types, true) === false) {
            throw new InvalidColumnType($type, self::$types);
        }

        $columnArgs = func_get_args();

        try {
            $columnArgs[] = StringValue::check($label);
        } catch (InvalidStringValue $e) {
            //
        }

        if ($format !== null) {
            $columnArgs[] = $format;
        }

        try {
            $role = StringValue::check($role);

            if (in_array($role, self::$roles) === false) {
                throw new InvalidColumnRole($role, self::$roles);
            }

            $columnArgs[] = $role;
        } catch (InvalidStringValue $e) {
            //
        }

        $column = new \ReflectionClass(__NAMESPACE__ . '\\Column');

        return $column->newInstanceArgs($columnArgs);
    }

    /**
     * Creates a new Column with the same values, while applying the Format.
     *
     * @param  \Khill\Lavacharts\DataTables\Columns\Column $column
     * @param  \Khill\Lavacharts\DataTables\Formats\Format $format
     * @return \Khill\Lavacharts\DataTables\Columns\Column
     */
    public function applyFormat(Column $column, Format $format)
    {
        return $this->create(
            $column->getType(),
            $column->getLabel(),
            $format,
            $column->getRole()
        );
    }
}
