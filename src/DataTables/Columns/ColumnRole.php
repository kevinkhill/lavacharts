<?php

namespace Khill\Lavacharts\DataTables\Columns;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Exceptions\InvalidColumnRole;

/**
 * ColumnRole Object
 *
 * The ColumnRole object is used to define the role that a column plays in a DataTable.
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
class ColumnRole implements \JsonSerializable
{
    /**
     * Types of valid column roles.
     *
     * @var array
     */
    private $roleTypes = [
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
     * Type of role.
     *
     * @var string
     */
    private $type;

    /**
     * Creates a new column role object.
     *
     * @access public
     * @param  string $type Type of role to create.
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnRole
     * @return self
     */
    public function __construct($type)
    {
        if (Utils::nonEmptyStringInArray($type, $this->roleTypes) === false) {
            throw new InvalidColumnRole($type, $this->roleTypes);
        }

        $this->type = $type;
    }

    /**
     * Custom json serialization of the column role.
     *
     * @access public
     * @return string
     */
    public function __toString()
    {
        return $this->type;
    }

    /**
     * Custom json serialization of the column role.
     *
     * @access public
     * @return array
     */
    public function jsonSerialize()
    {
        return ['role' => $this->type];
    }
}
