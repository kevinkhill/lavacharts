<?php

namespace Khill\Lavacharts\Builders;

use Khill\Lavacharts\Dashboards\Bindings\Binding;
use Khill\Lavacharts\Dashboards\Dashboard;
use Khill\Lavacharts\DataTables\DataTable;

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
class DashboardBuilder extends RenderableBuilder
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
     * @var Binding[]
     */
    protected $bindings = [];

    /**
     * Options for the chart.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Set the bindings for the Dashboard.
     *
     * @param  Binding[] $bindings Array of bindings
     * @return self
     */
    public function setBindings(array $bindings)
    {
        $this->bindings = $bindings;

        return $this;
    }

    /**
     * Sets the options for the chart.
     *
     * @param  array $options
     * @return self
     */
    public function setOptions($options)
    {
        $this->options = $options;

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
            $this->options
        );

        if (! empty($this->elementId)) {
            $dash->setElementId($this->elementId);
        }

        if (count($this->bindings) > 0) {
            $dash->setBindings($this->bindings);
        }

        return $dash;
    }
}
