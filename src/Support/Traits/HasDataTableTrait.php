<?php

namespace Khill\Lavacharts\Support\Traits;

use Khill\Lavacharts\DataTables\DataTable;
use Khill\Lavacharts\Support\Contracts\DataInterface;

/**
 * Trait DataTableTrait
 *
 * Provides common methods for working with DataTables.
 *
 *
 * @package   Khill\Lavacharts\Support\Traits
 * @since     3.1.6
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright 2020 Kevin Hill
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
trait HasDataTableTrait
{
    /**
     * Datatable for the renderable.
     *
     * @var DataTable
     */
    protected $datatable;

    /**
     * Sets the DataTable
     *
     * @since  3.1.0
     * @param  DataInterface $data
     */
    public function setDataTable(DataInterface $data)
    {
        $this->datatable = $data;
    }

    /**
     * Returns the DataTable
     *
     * @since  3.0.0
     * @return DataTable
     */
    public function getDataTable()
    {
        return $this->datatable;
    }
}
