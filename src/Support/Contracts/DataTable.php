<?php

namespace Khill\Lavacharts\Support\Contracts;

/**
 * DataTable Interface
 *
 * Provides common methods for working with DataTables.
 *
 *
 * @package   Khill\Lavacharts\Support\Contracts
 * @since     3.1.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
interface DataTable
{
    /**
     * Sets the DataTable
     *
     * @since 3.1.0
     * @param \Khill\Lavacharts\Support\Contracts\DataTable $datatable
     */
    public function setDataTable(DataTable $datatable);

    /**
     * Returns the DataTable
     *
     * @since  3.0.0
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public function getDataTable();

    /**
     * Returns a JSON string representation of the datatable.
     *
     * @since  2.5.0
     * @return string
     */
    public function getDataTableJson();
}
