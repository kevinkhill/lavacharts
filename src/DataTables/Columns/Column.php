<?php

namespace Khill\Lavacharts\DataTables\Columns;

use \Khill\Lavacharts\DataTables\Formats\Format;
use Khill\Lavacharts\Utils;

/**
 * Column Object
 *
 * The Column object is used to define the different columns for a DataTable.
 *
 *
 * @package    Khill\Lavacharts
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
     * Column type.
     *
     * @var string
     */
    protected $type = '';

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
     * @param  string $type Type of Column
     * @param  string $label Column label (optional).
     * @param  string $id Column ID (optional).
     * @throws \Khill\Lavacharts\DataTables\Columns\InvalidColumnRole
     */
    public function __construct($type, $label = '', $id = '')
    {
        $this->type  = $type;
        $this->label = $label;
        $this->id    = $id;
    }

    /**
     * Returns the type of column.
     *
     * @access public
     * @return string Column type.
     */
    public function getType()
    {
        return $this->type;
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

    /**
     * Returns the column id.
     *
     * @access public
     * @return string Column id.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the column formatter.
     *
     * @access public
     * @return \Khill\Lavacharts\DataTables\Columns\Column
     * @param  \Khill\Lavacharts\DataTables\Formats\Format
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
     * Returns the status of if the column is formatted.
     *
     * @access public
     * @return boolean
     */
    public function isFormatted()
    {
        return ($this->format instanceof Format);
    }

    /**
     * Sets the column formatter.
     *
     * @access public
     * @return \Khill\Lavacharts\DataTables\Columns\Column
     * @param  \Khill\Lavacharts\DataTables\Columns\ColumnRole
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
            'type' => $this->type
        ];

        if (Utils::nonEmptyString($this->label) === true) {
            $values['label'] = (string) $this->label;
        }

        if (Utils::nonEmptyString($this->id) === true) {
            $values['id'] = (string) $this->id;
        }

        if ($this->role instanceof ColumnRole) {
            $values['p'] = $this->role;
        }

        return $values;
    }
}
