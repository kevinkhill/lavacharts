<?php

namespace Khill\Lavacharts\DataTables\Columns;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\DataTables\Formats\Format;
use \Khill\Lavacharts\Exceptions\InvalidColumnRole;
use \Khill\Lavacharts\Exceptions\InvalidColumnType;

/**
 * ColumnFactory Class
 *
 * The ColumnFactory creates new columns for DataTables. The only mandatory parameter is
 * the type of column to create, all others are optional.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage DataTables\Columns
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class ColumnFactory
{
    /**
     * Valid column types
     *
     * @var array
     */
    public static $TYPES = [
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
    public static $ROLES = [
        'annotation',
        'annotationText',
        'certainty',
        'emphasis',
        'interval',
        'scope',
        'style',
        'tooltip',
        'data',
        'domain'
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
    public static function create($type, $label = '', Format $format = null, $role = '')
    {
        if (Utils::nonEmptyStringInArray($type, self::$TYPES) === false) {
            throw new InvalidColumnType($type, self::$TYPES);
        }

        $columnArgs = [$type];

        if (Utils::nonEmptyString($label) === true) {
            $columnArgs[] = $label;
        } else {
            $columnArgs[] = '';
        }

        if ($format !== null) {
            $columnArgs[] = $format;
        } else {
            $columnArgs[] = null;
        }

        if (is_string($role) === false || ($role != '' && in_array($role, self::$ROLES, true) === false)) {
            throw new InvalidColumnRole($role, self::$ROLES);
        }

        $columnArgs[] = $role;

        $column = new \ReflectionClass('\Khill\Lavacharts\DataTables\Columns\Column');

        return $column->newInstanceArgs($columnArgs);
    }

    /**
     * Creates a new Column with the same values, while applying the Format.
     *
     * @param  \Khill\Lavacharts\DataTables\Columns\Column $column
     * @param  \Khill\Lavacharts\DataTables\Formats\Format $format
     * @return \Khill\Lavacharts\DataTables\Columns\Column
     */
    public static function applyFormat(Column $column, Format $format)
    {
        return ColumnFactory::create($column->getType(), $column->getLabel(), $format, $column->getRole());
    }
}
