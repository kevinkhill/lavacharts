<?php

namespace Khill\Lavacharts\DataTables\Rows;

use Carbon\Carbon;
use Khill\Lavacharts\DataTables\DataTable;
use Khill\Lavacharts\DataTables\Cells\DateCell;
use Khill\Lavacharts\Exceptions\InvalidCellCount;
use Khill\Lavacharts\Exceptions\InvalidRowDefinition;

/**
 * RowFactory Class
 *
 * The RowFactory creates new rows for the DataTables.
 *
 *
 * @package   Khill\Lavacharts\DataTables\Rows
 * @since     3.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2016, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
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
     */
    public function __construct(DataTable $datatable)
    {
        $this->datatable = $datatable;
    }

    
}
