<?php

namespace Khill\Lavacharts\Support\Contracts;

/**
 * DataTable Interface
 *
 * Provides common methods for working with DataTables.
 *
 *
 * @package   Khill\Lavacharts\Support\Contracts
 * @since     3.1.6
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
interface DataInterface
{
    /**
     * Returns the DataTable
     *
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public function getDataTable();
}
