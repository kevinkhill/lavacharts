<?php

namespace Khill\Lavacharts\Builders;

use Khill\Lavacharts\Dashboards\Dashboard;

//@TODO: phpdocs!
class DashboardBuilder extends GenericBuilder
{
    private $bindings = null;

    public function setBindings($bindings)
    {
        $this->bindings = $bindings;

        return $this;
    }

    public function getDashboard()
    {
        return new Dashboard(
            $this->label,
            $this->bindings,
            $this->elementId
        );
    }
}
