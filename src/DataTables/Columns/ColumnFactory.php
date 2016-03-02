<?php

namespace Khill\Lavacharts\DataTables\Columns;

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
 * @copyright  (c) 2016, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class ColumnFactory
{
    use \Khill\Lavacharts\Traits\NonEmptyStringTrait;

    /**
     * Valid column types
     *
     * @var array
     */
    public $types = [
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
    public $roles = [
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
    public $desc = [
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
        $this->typeCheck($type);

        $columnArgs = func_get_args();

        if ($this->nonEmptyString($label)) {
            $columnArgs[] = $label;
        }

        if ($format !== null) {
            $columnArgs[] = $format;
        }

        if ($this->nonEmptyString($role) === true && in_array($role, $this->roles, true) === false) {
            throw new InvalidColumnRole($role, $this->roles);
        }

        $columnArgs[] = $role;

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

    /**
     * Checks if the given type is a valid column type
     *
     * @param  string $type
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnType
     */
    public function typeCheck($type)
    {
        if (in_array($type, $this->types, true) === false) {
            throw new InvalidColumnType($type, $this->types);
        }
    }

    /**
     * Checks if the given role is a valid column role
     *
     * @param  string $role
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnRole
     */
    public function roleCheck($role)
    {
        if (in_array($role, $this->roles, true) === false) {
            throw new InvalidColumnRole($role, $this->types);
        }
    }
}
