<?php

namespace Khill\Lavacharts\DataTables\Columns;

use \Khill\Lavacharts\DataTables\Formats\Format;
use \Khill\Lavacharts\Support\Traits\NonEmptyStringTrait as StringCheck;

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
 * @copyright  (c) 2016, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class Column implements \JsonSerializable
{
    use StringCheck;

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
     * @param  string                                      $type   Column type.
     * @param  string                                      $label  Column label (optional).
     * @param  \Khill\Lavacharts\DataTables\Formats\Format $format Column format(optional).
     * @param  string                                      $role   Column role (optional).
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
     * @return \Khill\Lavacharts\DataTables\Formats\Format
     */
    public function getFormat()
    {
        return $this->format;
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
     * Custom json serialization of the column.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $values = [
            'type' => $this->type
        ];

        if ($this->nonEmptyString($this->label) === true) {
            $values['label'] = $this->label;
        }

        if ($this->nonEmptyString($this->role) === true) {
            $values['p'] = ['role' => $this->role];
        }

        return $values;
    }
}
