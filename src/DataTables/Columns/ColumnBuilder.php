<?php

namespace Khill\Lavacharts\DataTables\Columns;

use Khill\Lavacharts\DataTables\Formats\Format;
use Khill\Lavacharts\Exceptions\InvalidColumnRole;
use Khill\Lavacharts\Exceptions\InvalidColumnType;
use Khill\Lavacharts\Support\Customizable;
use Khill\Lavacharts\Values\Role;
use Khill\Lavacharts\Values\StringValue;

/**
 * Column Object
 *
 * The Column object is used to define the different columns for a DataTable.
 *
 *
 * @package   Khill\Lavacharts\DataTables\Columns
 * @since     3.1.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class ColumnBuilder
{
    /**
     * Column type.
     *
     * @var string
     */
    private $type;

    /**
     * Column label.
     *
     * @var string
     */
    private $label = '';

    /**
     * Column formatter.
     *
     * @var \Khill\Lavacharts\DataTables\Formats\Format
     */
    private $format = null;

    /**
     * Column role.
     *
     * @var \Khill\Lavacharts\Values\Role
     */
    private $role = null;

    /**
     * Column options
     *
     * @var array
     */
    private $options = [];

    /**
     * Sets the type of column.
     *
     * @param  string $type
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnType
     */
    public function setType($type)
    {
        if (StringValue::isNonEmpty($type) === false) {
            throw new InvalidColumnType($type);
        }

        $this->type = $type;
    }

    /**
     * Sets the column label.
     *
     * @param  string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Sets the column formatter.
     *
     * @param \Khill\Lavacharts\DataTables\Formats\Format $format
     */
    public function setFormat(Format $format = null)
    {
        $this->format = $format;
    }

    /**
     * Sets the column role.
     *
     * @param  string $role
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnRole
     */
    public function setRole($role)
    {
        if (StringValue::isNonEmpty($role)) {
            $this->role = new Role($role);
        }
    }

    /**
     * Sets the column options.
     *
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * Creates a new column instance with the set values.
     *
     * @return \Khill\Lavacharts\DataTables\Columns\Column
     */
    public function getResult()
    {
        return new Column(
            $this->type,
            $this->label,
            $this->format,
            $this->role,
            $this->options
        );
    }
}
