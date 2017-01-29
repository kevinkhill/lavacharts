<?php

namespace Khill\Lavacharts\DataTables\Columns;

use Khill\Lavacharts\DataTables\Formats\Format;
use Khill\Lavacharts\Support\Customizable;
use Khill\Lavacharts\Values\Role;

/**
 * Column Object
 *
 * The Column object is used to define the different columns for a DataTable.
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
class Column extends Customizable
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
    protected $role = null;

    /**
     * Creates a new Column with the defined label.
     *
     * @param  string                                      $type    Column type.
     * @param  string                                      $label   Column label (optional).
     * @param  \Khill\Lavacharts\DataTables\Formats\Format $format  Column format(optional).
     * @param  \Khill\Lavacharts\Values\Role               $role    Column role (optional).
     * @param  array                                       $options Column options (optional).
     */
    public function __construct($type, $label = '', Format $format = null, Role $role = null, array $options = [])
    {
        parent::__construct($options);

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

        if (is_string($this->label) && !empty($this->label)) {
            $values['label'] = $this->label;
        }

        if ($this->role instanceof Role) {
            $values['p'] = ['role' => (string) $this->role];

            if ($this->hasOptions()) {
                $values['p'] = array_merge($values['p'], $this->getOptions());
            }
        }

        return $values;
    }
}
