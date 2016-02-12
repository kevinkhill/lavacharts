<?php

namespace Khill\Lavacharts\Dashboards;

use \Khill\Lavacharts\DataTables\DataTable;
use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Values\ElementId;

//@TODO: phpdocs!
class DashboardBuilder
{
    private $label = null;
    private $bindings = null;
    private $elementId = null;

    public function setLabel($label)
    {
        $this->label = new Label($label);

        return $this;
    }

    public function setBindings($bindings)
    {
        $this->bindings = $bindings;

        return $this;
    }

    public function setElementId($elementId)
    {
        $this->elementId = new ElementId($elementId);

        return $this;
    }

    public function getDashboard()
    {
        $chart = __NAMESPACE__ . '\\' . $this->type;

        return new Dashboard(
            $this->label,
            $this->bindings,
            $this->elementId
        );
    }
}
