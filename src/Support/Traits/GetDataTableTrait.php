<?php

namespace Khill\Lavacharts\Support\Traits;

/**
 * Trait ToDataTableTrait
 *
 * Apply this trait to any class to enable the automatic casting to a DataTables.
 *
 * With this trait, and the user implementation of the methods getRows() and getColumns(),
 * then the class is capable of being used as a DataTable. The class implementing the
 * DataTableInterface can now be passed as the DataTable while creating charts.
 *
 * @see \Khill\Lavacharts\Support\Contracts\DataTableInterface
 *
 * @package   Khill\Lavacharts\Support\Traits
 * @since     3.1.6
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
trait GetDataTableTrait
{
    /**
     * Create a new DataTable from column and row definitions.
     *
     * @return \Khill\Lavacharts\DataTables\DataTable
     * @throws \Khill\Lavacharts\Exceptions\DataTableCastingException
     */
    public function getDataTable()
    {
        $data = new \Khill\Lavacharts\DataTables\DataTable;

        if (method_exists($this, 'getColumns')) {
            $data->addColumns($this->getColumns());
        }

        if (method_exists($this, 'getRows')) {
            $data->addRows($this->getRows());
        }

        return $data;
    }
}

