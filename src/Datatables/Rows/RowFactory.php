<?php

namespace Khill\Lavacharts\DataTables\Rows;

use \Khill\Lavacharts\DataTables\DataTable;
use \Khill\Lavacharts\Exceptions\InvalidCellCount;

/**
 * RowFactory Class
 *
 * The RowFactory creates new rows for the DataTables.
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
class RowFactory
{
    /**
     * DataTable to reference when creating new rows.
     *
     * @var \Khill\Lavacharts\DataTables\DataTable
     */
    private $datatable;

    /**
     * Creates a new RowFactory instance.
     *
     * @access public
     * @param  \Khill\Lavacharts\DataTables\DataTable $datatable
     * @return self
     */
    public function __construct(DataTable $datatable)
    {
        $this->datatable = $datatable;
    }

    /**
     * Creates a new Row object.
     *
     * @param  array $valueArray Array of values to assign to the row.
     * @throws \Khill\Lavacharts\Exceptions\InvalidCellCount
     * @return \Khill\Lavacharts\DataTables\Rows\Row
     */
    public function create($valueArray)
    {
        $columnCount  = $this->datatable->getColumnCount();
        $rowCellCount = count($valueArray);

        if ($rowCellCount > $columnCount) {
            throw new InvalidCellCount($rowCellCount, $columnCount);
        }

        return new Row($valueArray);
    }

    /**
     * Creates a new NullRow
     *
     * @return \Khill\Lavacharts\DataTables\Rows\NullRow
     */
    public function null()
    {
        return new NullRow($this->datatable->getColumnCount());
    }
}
