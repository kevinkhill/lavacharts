<?php

namespace Khill\Lavacharts\DataTables\Columns;

use \Khill\Lavacharts\DataTables\Formats\Format;

/**
 * Column Object
 *
 * The Column object is used to define the different columns for a DataTable.
 *
 *
 * @package    Lavacharts
 * @subpackage DataTables\Columns
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class Column implements \JsonSerializable
{
    /**
     * Column label.
     *
     * @var string
     */
    protected $label = '';

    /**
     * Column ID.
     *
     * @var string
     */
    protected $id = '';

    /**
     * Column formatter.
     *
     * @var \Khill\Lavacharts\DataTables\Formats\Format
     */
    protected $format = null;

    /**
     * Column role.
     *
     * @var string
     */
    protected $role = null;

    /**
     * Creates a new Column with the defined label.
     *
     * @access public
     * @param  string $label Column label (optional).
     * @return self
     */
    public function __construct($label='')
    {
        $this->label = $label;
    }

    /**
     * Returns the type of column.
     *
     * @access public
     * @return string Column type.
     */
    public function getType()
    {
        return static::TYPE;
    }

    /**
     * Returns the column label.
     *
     * @access public
     * @return string Column label.
     */
    public function getLabel()
    {
        return $this->label;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the column formatter.
     *
     * @access public
     * @param  \Khill\Lavacharts\DataTables\Formats\Format
     * @return self
     */
    public function setFormat(Format $format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Returns the column formatter.
     *
     * @access public
     * @return \Khill\Lavacharts\DataTables\Formats\Format
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Sets the column formatter.
     *
     * @access public
     * @param  \Khill\Lavacharts\DataTables\Columns\ColumnRole
     * @return self
     */
    public function setRole(ColumnRole $role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Returns the column role.
     *
     * @access public
     * @return \Khill\Lavacharts\DataTables\Columns\ColumnRole
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Custom json serialization of the column.
     *
     * @access public
     * @return array
     */
    public function jsonSerialize()
    {
        $values = [
            'type'  => static::TYPE,
            'label' => (string) $this->label,
            'id'    => (string) $this->id,
        ];
/*
        TODO: fix and check for formatters
        if ($this->format instanceof Format) {
            $values['f'] = $this->format;
        }
*/
        if ($this->role instanceof ColumnRole) {
            $values['p'] = $this->role;
        }

        return $values;
    }
}

