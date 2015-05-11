<?php

namespace Khill\Lavacharts\Dashboard;

use \Khill\Lavacharts\Dashboard\Binding;

class Dashboard implements \JsonSerializable {

    /**
     * Javascript chart package.
     *
     * @var string
     */
    const VIZ_PACKAGE = 'controls';

    /**
     * Javascript chart class.
     *
     * @var string
     */
    const VIZ_CLASS = 'google.visualization.Dashboard';

    /**
     * The dashboard's unique label.
     *
     * @var string
     */
    public $label = null;

    /**
     * Arry of Binding objects, mapping controls to charts.
     *
     * @var array
     */
    private $bindings = [];

    /**
     * Builds a new Dashboard with identifying label.
     *
     * @param  string $label
     * @return self
     */
    public function __construct($label)
    {
        if (Utils::nonEmptyString($chartLabel) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string',
                'which is unique and non-empty'
            );
        }

        $this->label = $label;
    }

    /**
     * Binds a ControlWrapper to a ChartWrapper in the dashboard.
     *
     * @param  \Khill\Lavacharts\Dashboard\ChartWrapper   $chartWrap
     * @param  \Khill\Lavacharts\Dashboard\ControlWrapper $controlWrap
     * @return self
     */
    public function bind(ControlWrapper $controlWrap, ChartWrapper $chartWrap)
    {
        $this->bindings[] = new Binding($controlWrap, $chartWrap);

        return $this;
    }

    public function jsonSerialize() {
        return $this->bindings;
    }
}
