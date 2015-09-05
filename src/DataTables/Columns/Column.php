<?php

namespace Khill\Lavacharts\DataTables\Columns;


use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\DataTables\Formats\Format;

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
    protected $type;

    /**
     * Column label.
     *
     * @var string
     */
    protected $label = '';

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
    protected $role = '';

    /**
     * Creates a new Column with the defined label.
     *
     * @access public
     * @param  string                                      $type Type of Column
     * @param  string                                      $label Column label (optional).
     * @param  \Khill\Lavacharts\DataTables\Formats\Format $format
     * @param  string                                      $role Column role (optional).
     */
    public function __construct($type, $label = '', Format $format = null, $role = '')
    {
        $this->type   = $type;
        $this->label  = $label;
        $this->format = $format;
        $this->role   = $role;
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
     * Returns the column role.
     *
     * @access public
     * @return string
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
            $values['label'] = $this->label;
        }

        if (Utils::nonEmptyString($this->role) === true) {
            $values['p'] = ['role' => $this->role];
        }

        return $values;
    }
}
