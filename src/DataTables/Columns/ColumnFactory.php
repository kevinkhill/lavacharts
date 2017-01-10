<?php

namespace Khill\Lavacharts\DataTables\Columns;

use Khill\Lavacharts\Values\Role;
use Khill\Lavacharts\Values\StringValue;
use Khill\Lavacharts\DataTables\Formats\Format;
use Khill\Lavacharts\Exceptions\InvalidColumnType;
use Khill\Lavacharts\Exceptions\InvalidColumnRole;

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
 * @copyright (c) 2017, KHill Designs
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
     * Creates a new column object.
     *
     * @access public
     * @since  3.0.0
     * @param  string                                      $type    Type of column to create.
     * @param  string                                      $label   A label for the column.
     * @param  \Khill\Lavacharts\DataTables\Formats\Format $format  Column formatter for the data.
     * @param  string                                      $role    A role for the column to play.
     * @param  array                                       $options Column options.
     * @return \Khill\Lavacharts\DataTables\Columns\Column
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnRole
     */
    public function create(
        $type,
        $label = '',
        Format $format = null,
        $role = '',
        array $options = []
    ) {
        self::isValidType($type);

        $builder = new ColumnBuilder();
        $builder->setType($type);
        $builder->setLabel($label);
        $builder->setFormat($format);
        $builder->setRole($role);
        $builder->setOptions($options);

        return $builder->getResult();
    }

    /**
     * Creates a new Column with the same values, while applying the Format.
     *
     * @param  \Khill\Lavacharts\DataTables\Columns\Column $column
     * @param  \Khill\Lavacharts\DataTables\Formats\Format $format
     * @return \Khill\Lavacharts\DataTables\Columns\Column
     */
    public function applyFormat(Column $column, Format $format = null)
    {
        return $this->create(
            $column->getType(),
            $column->getLabel(),
            $format,
            $column->getRole()
        );
    }

    /**
     * Checks if a given type is a valid column type
     *
     * @param  string $type
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnType
     */
    public static function isValidType($type)
    {
        if (in_array($type, self::$types, true) === false) {
            throw new InvalidColumnType($type, self::$types);
        }
    }
}
