<?php

namespace Khill\Lavacharts\DataTables\Rows;

/**
 * NullRow Object
 *
 * The null row object is used to add an empty row to the datatable.
 *
 *
 * @package    Lavacharts
 * @subpackage DataTables\Rows
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class NullRow extends Row
{
    /**
     * Creates a new NullRow obeject
     *
     * @param  int $numOfCols Number of null columns to create.
     * @return self
     */
    public function __construct($numOfCols)
    {
        for ($a = 0; $a < $numOfCols; $a++) {
            $tmp[] = null;
        }

        parent::__construct($tmp);
    }
}
