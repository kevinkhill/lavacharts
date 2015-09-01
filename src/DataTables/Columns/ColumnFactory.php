<?php

namespace Khill\Lavacharts\DataTables\Columns;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\DataTables\Formats\Format;
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
     * Valid column roles
     *
     * @var array
     */
    private static $columnTypes = [
        'role',
        'string',
        'number',
        'boolean',
        'date',
        'datetime',
        'timeofday'
    ];

    /**
     * Valid column descriptions
     *
     * @var array
     */
    private static $columnDesc = [
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
     * @since 3.0.0
     * @param  string $type Type of column to create.
     * @param  string $label A label for the column.
     * @param  \Khill\Lavacharts\DataTables\Formats\Format $format Column formatter for the data.
     * @param  string $role A role for the column to play.
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnType
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnRole
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnFormat
     * @return \Khill\Lavacharts\DataTables\Columns\Column
     */
    public static function create($type, $label = '', Format $format = null, $role = '')
    {
        if (Utils::nonEmptyStringInArray($type, self::$columnTypes) === false) {
            throw new InvalidColumnType($type, self::$columnTypes);
        }

        if ($type == 'datetime') {
            $type = 'dateTime';
        }

        if ($type == 'timeofday') {
            $type = 'timeOfDay';
        }

        $columnType = __NAMESPACE__ . '\\' . ucfirst($type) . 'Column';

        $column = new $columnType($label);

        if ($format instanceof Format) {
            $column->setFormat($format);
        }

        if (Utils::nonEmptyString($role)) {
            $column->setRole(new ColumnRole($role));
        }

        return $column;
    }
}
