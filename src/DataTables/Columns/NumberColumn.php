<?php

namespace Khill\Lavacharts\DataTables\Columns;

/**
 * NumberColumn Object
 *
 * Number columns are for int|float type data in the DataTable.
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
class NumberColumn extends Column
{
    /**
     * Type of column.
     *
     * @var string
     */
    const TYPE = 'number';

    /**
     * Creates a new column object.
     *
     * @access public
     * @param  string $label Label for the column.
     * @return self
     */
    public function __construct($label = '')
    {
        parent::__construct($label);
    }
}
