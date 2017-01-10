<?php

namespace Khill\Lavacharts\Builders;

use \Khill\Lavacharts\Dashboards\Dashboard;
use \Khill\Lavacharts\DataTables\DataTable;

/**
 * Class DashboardBuilder
 *
 * This class is used to build dashboards by setting the properties, instead of trying to cover
 * everything in the constructor.
 *
 * @package    Khill\Lavacharts\Builders
 * @since      3.0.3
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2017, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class DashboardBuilder extends GenericBuilder
{
    /**
     * Datatable for the chart.
     *
     * @var \Khill\Lavacharts\DataTables\DataTable
     */
    protected $datatable = null;

    /**
     * Bindings to use for the dashboard.
     *
     * @var \Khill\Lavacharts\Dashboards\Bindings\Binding[]
     */
    protected $bindings = [];

    /**
     * Set the bindings for the Dashboard.
     *
     * @param  \Khill\Lavacharts\Dashboards\Bindings\Binding[] $bindings Array of bindings
     * @return $this
     */
    public function setBindings(array $bindings)
    {
        $this->bindings = $bindings;

        return $this;
    }

    /**
     * Set the DataTable for the dashboard
     *
     * @param \Khill\Lavacharts\DataTables\DataTable $datatable
     * @return $this
     */
    public function setDataTable(DataTable $datatable)
    {
        $this->datatable = $datatable;

        return $this;
    }

    /**
     * Returns the built Dashboard.
     *
     * @return \Khill\Lavacharts\Dashboards\Dashboard
     */
    public function getDashboard()
    {
        $dash = new Dashboard(
            $this->label,
            $this->datatable,
            $this->elementId
        );

        return $dash->setBindings($this->bindings);
    }
}
