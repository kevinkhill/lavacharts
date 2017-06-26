<?php

namespace Khill\Lavacharts\Support\Traits;

use Khill\Lavacharts\DataTables\DataTable;
use \Khill\Lavacharts\Support\Contracts\DataTable as DataInterface;

/**
 * Trait DataTableTrait
 *
 * Provides common methods for working with DataTables.
 *
 *
 * @package   Khill\Lavacharts\Support\Traits
 * @since     3.1.6
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
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
    private $datatable;

    /**
     * Sets the DataTable
     *
     * @since  3.1.0
     * @param  DataInterface $data
     * @return self
     */
    public function setDataTable(DataInterface $data)
    {
        $this->datatable = $data->getDataTable();

        return $this;
    }

    /**
     * Returns the DataTable
     *
     * @since  3.0.0
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public function getDataTable()
    {
        return $this->datatable;
    }
}
